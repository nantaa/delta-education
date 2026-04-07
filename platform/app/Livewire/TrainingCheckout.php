<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Training;
use App\Models\Order;
use App\Models\Participant;
use App\Models\DiscountCode;
use App\Services\PaymentGatewayService;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class TrainingCheckout extends Component
{
    public Training $training;

    // Core details
    public string $name = '';
    public string $email = '';
    public string $phone = '';

    public string $discount_code = '';
    /** Store only the ID to avoid Livewire serialisation issues */
    public ?int $appliedDiscountId = null;

    public bool $privacy_consent = false;

    // Payment
    public ?string $snapToken = null;
    public bool $isFree = false;
    public bool $success = false;
    public $createdOrderId;

    public float $effectivePrice = 0;
    public float $originalPrice  = 0;

    protected function rules(): array
    {
        return [
            'name'            => 'required|min:3|max:191',
            'email'           => 'required|email|max:191',
            'phone'           => 'required|numeric|min_digits:9|max_digits:15',
            'privacy_consent' => 'accepted',
        ];
    }

    protected $messages = [
        'privacy_consent.accepted' => 'Anda harus menyetujui kebijakan privasi.',
    ];

    public function mount(string $slug): void
    {
        $this->training      = Training::where('slug', $slug)->firstOrFail();
        $this->originalPrice = (float) $this->training->price;
        $this->effectivePrice = $this->originalPrice;
        $this->isFree        = $this->effectivePrice <= 0;
    }

    /** Resolved lazily so we always have fresh data */
    public function getAppliedDiscountProperty(): ?DiscountCode
    {
        return $this->appliedDiscountId
            ? DiscountCode::find($this->appliedDiscountId)
            : null;
    }

    public function applyDiscount(): void
    {
        $this->discount_code = strtoupper(trim($this->discount_code));

        if ($this->discount_code === '') {
            $this->resetDiscount();
            return;
        }

        $code = DiscountCode::where('code', $this->discount_code)->first();

        if ($code && $code->isValid()) {
            $this->appliedDiscountId = $code->id;
            $this->effectivePrice    = $code->calculateDiscount($this->originalPrice);
            $this->isFree            = $this->effectivePrice <= 0;
            session()->flash('discount_success', 'Kode diskon berhasil diaplikasikan!');
        } else {
            $this->resetDiscount();
            session()->flash('discount_error', 'Kode diskon tidak valid atau telah kedaluwarsa.');
        }
    }

    private function resetDiscount(): void
    {
        $this->appliedDiscountId = null;
        $this->effectivePrice    = $this->originalPrice;
        $this->isFree            = $this->effectivePrice <= 0;
    }

    public function submit(): void
    {
        $this->validate();

        // Guard: re-validate duplicate email for this training
        $alreadyRegistered = Participant::where('participatable_id', $this->training->id)
            ->where('participatable_type', Training::class)
            ->where('email', $this->email)
            ->exists();

        if ($alreadyRegistered) {
            $this->addError('email', 'Email ini sudah terdaftar untuk pelatihan ini.');
            return;
        }

        // Guard: capacity check
        if ($this->training->participants()->count() >= $this->training->capacity) {
            session()->flash('error', 'Maaf, kuota pelatihan ini sudah penuh.');
            return;
        }

        // Re-validate discount freshness
        $discount = $this->appliedDiscountId ? DiscountCode::find($this->appliedDiscountId) : null;
        if ($discount && $discount->isValid()) {
            $this->effectivePrice = $discount->calculateDiscount($this->originalPrice);
            $this->isFree         = $this->effectivePrice <= 0;
        } else {
            $this->resetDiscount();
            $discount = null;
        }

        $participant = new Participant([
            'name'            => $this->name,
            'email'           => $this->email,
            'whatsapp_number' => $this->phone,
            'privacy_consent' => $this->privacy_consent,
            'payment_status'  => $this->isFree ? 'settlement' : 'pending',
            'amount_paid'     => $this->effectivePrice,
            'discount_code_id' => $discount?->id,
            'discount_amount'  => $discount ? ($this->originalPrice - $this->effectivePrice) : null,
        ]);

        $this->training->participants()->save($participant);

        if ($discount) {
            $discount->increment('used_count');
        }

        if ($this->isFree) {
            $this->success = true;
            return;
        }

        // Create Order for Midtrans
        $order = Order::create([
            'customer_name'  => $this->name,
            'customer_email' => $this->email,
            'customer_phone' => $this->phone,
            'total_amount'   => $this->effectivePrice,
            'status'         => 'pending',
            'payment_method' => 'midtrans_snap',
        ]);

        $order->items()->create([
            'productable_id'   => $this->training->id,
            'productable_type' => Training::class,
            'item_name'        => $this->training->title,
            'price'            => $this->effectivePrice,
        ]);

        $participant->update(['transaction_id' => (string) $order->id]);
        
        $this->createdOrderId = $order->id;

        $paymentGateway = new PaymentGatewayService();
        $snapToken = $paymentGateway->getSnapToken($order);

        if ($snapToken) {
            $this->snapToken = $snapToken;
            $this->dispatch('open-snap', token: $snapToken);
        } else {
            session()->flash('error', 'Gagal menginisiasi pembayaran. Silakan coba lagi.');
        }
    }

    public function handlePaymentSuccess()
    {
        $this->success = true;
        // Optionally flash a message or perform additional checks
    }

    public function render()
    {
        return view('livewire.training-checkout');
    }
}
