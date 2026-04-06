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
    public string $phone = ''; // Maps to whatsapp_number

    public string $discount_code = '';
    public ?DiscountCode $appliedDiscount = null;

    public bool $privacy_consent = false;

    // Payment
    public ?string $snapToken = null;
    public bool $isFree = false;
    public bool $success = false;

    public float $effectivePrice = 0;

    protected function rules()
    {
        return [
            'name'            => 'required|min:3|max:191',
            'email'           => 'required|email|max:191|unique:participants,email,NULL,id,participatable_id,' . $this->training->id . ',participatable_type,' . Training::class,
            'phone'           => 'required|numeric|min_digits:9|max_digits:15',
            'privacy_consent' => 'accepted',
        ];
    }

    protected $messages = [
        'email.unique'             => 'Email ini sudah terdaftar untuk pelatihan ini.',
        'privacy_consent.accepted' => 'Anda harus menyetujui kebijakan privasi.',
    ];

    public function mount($slug)
    {
        $this->training = Training::where('slug', $slug)->firstOrFail();
        $this->effectivePrice = $this->training->price;
        $this->isFree = $this->effectivePrice <= 0;
    }

    public function updatedDiscountCode()
    {
        $this->discount_code = strtoupper(trim($this->discount_code));
        $code = DiscountCode::where('code', $this->discount_code)->first();

        if ($code && $code->isValid()) {
            $this->appliedDiscount = $code;
            $this->effectivePrice = $code->calculateDiscount($this->training->price);
            $this->isFree = $this->effectivePrice <= 0;
            session()->flash('discount_success', 'Kode diskon berhasil diaplikasikan!');
        } else {
            $this->appliedDiscount = null;
            $this->effectivePrice = $this->training->price;
            $this->isFree = $this->effectivePrice <= 0;
            if ($this->discount_code !== '') {
                session()->flash('discount_error', 'Kode diskon tidak valid atau telah kedaluwarsa.');
            }
        }
    }

    public function submit()
    {
        $this->validate();

        // Safety check to re-calculate discount just before submit
        $this->updatedDiscountCode();

        $participant = new Participant([
            'name'            => $this->name,
            'email'           => $this->email,
            'whatsapp_number' => $this->phone,
            'privacy_consent' => $this->privacy_consent,
            'payment_status'  => $this->isFree ? 'settlement' : 'pending',
            'amount_paid'     => $this->effectivePrice,
        ]);

        if ($this->appliedDiscount) {
            $participant->discount_code_id = $this->appliedDiscount->id;
            $participant->discount_amount = $this->training->price - $this->effectivePrice;
            
            // Increment usage limit for the discount code
            $this->appliedDiscount->increment('used_count');
        }

        $this->training->participants()->save($participant);

        if ($this->isFree) {
            $this->success = true;
            return;
        }

        // Create Order for tracking and Midtrans
        $order = Order::create([
            'order_type'    => 'training',
            'order_number'  => 'TRN-' . strtoupper(uniqid()),
            'name'          => $this->name,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'total_amount'  => $this->effectivePrice,
            'status'        => 'pending',
        ]);

        $order->items()->create([
            'purchasable_id'   => $this->training->id,
            'purchasable_type' => Training::class,
            'item_name'        => $this->training->title,
            'price'            => $this->effectivePrice,
            'quantity'         => 1,
        ]);

        $participant->update([
            'transaction_id' => $order->order_number
        ]);

        $paymentGateway = new PaymentGatewayService();
        $this->snapToken = $paymentGateway->getSnapToken($order);

        $this->dispatch('open-snap', token: $this->snapToken);
    }
}
