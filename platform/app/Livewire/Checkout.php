<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Webinar;
use App\Models\Product;
use App\Models\Order;
use App\Models\Participant;
use App\Services\PaymentGatewayService;
use App\Jobs\SendRegistrationConfirmation;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class Checkout extends Component
{
    public Model $purchasable;
    public string $type;
    
    // Core details
    public string $name = '';
    public string $email = '';
    public string $phone = ''; // Maps to whatsapp_number
    
    // Participant specific details
    public ?int $age = null;
    public ?string $gender = null;
    public ?string $domicile = null;
    public ?string $last_education = null;
    
    // Conditional details
    public string $education_status = 'Tidak Sedang Menempuh';
    public ?string $institution_name = null;
    
    public string $employment_status = 'Tidak Sedang Bekerja';
    public ?string $current_job = null;
    public ?string $company = null;
    
    public ?string $background = null;
    public ?string $event_source = null;
    public bool $privacy_consent = false;
    
    // Payment
    public ?string $snapToken = null;
    public bool $isFree = false;
    public bool $success = false;

    public function mount($type, $slug)
    {
        $this->type = $type;
        
        if ($type === 'webinar') {
            $this->purchasable = Webinar::where('slug', $slug)->firstOrFail();
        } elseif ($type === 'ebook' || $type === 'minicourse') {
            $this->purchasable = Product::where('slug', $slug)->firstOrFail();
        } else {
            abort(404);
        }

        $this->isFree = $this->purchasable->price <= 0;
    }

    public function processCheckout(PaymentGatewayService $paymentService)
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'privacy_consent' => 'accepted',
            'domicile' => 'nullable|string',
            'gender' => 'nullable|string',
            'age' => 'nullable|integer',
            'education_status' => 'required|string',
            'employment_status' => 'required|string',
        ]);

        if ($this->education_status === 'Sedang Menempuh') {
            $this->validate(['institution_name' => 'required|string|max:255']);
        }
        if ($this->employment_status === 'Sedang Bekerja') {
            $this->validate([
                'current_job' => 'required|string|max:255',
                'company' => 'required|string|max:255',
            ]);
        }

        if ($this->type === 'webinar') {
            $exists = Participant::where('participatable_id', $this->purchasable->id)
                ->where('participatable_type', get_class($this->purchasable))
                ->where('email', $this->email)
                ->exists();
                
            if ($exists) {
                $this->addError('email', 'This email is already registered for this event.');
                return;
            }
            
            if ($this->purchasable->participants()->count() >= $this->purchasable->capacity) {
                $this->addError('capacity', 'Sorry, this webinar is fully booked.');
                return;
            }
        }

        if ($this->isFree) {
            $this->handleFreeAccess();
            return;
        }

        // Paid Flow: Save participant as pending, let Midtrans handle the callback
        $participant = $this->saveParticipant('pending');

        $order = Order::create([
            'customer_name' => $this->name,
            'customer_email' => $this->email,
            'customer_phone' => $this->phone,
            'total_amount' => $this->purchasable->price,
            'status' => 'pending',
            'payment_method' => 'midtrans_snap'
        ]);

        // Link the participant to order if you have foreign key, otherwise order isolates
        $order->items()->create([
            'productable_id' => $this->purchasable->id,
            'productable_type' => get_class($this->purchasable),
            'item_name' => $this->purchasable->title,
            'price' => $this->purchasable->price,
        ]);

        $participant->update(['transaction_id' => (string) $order->id]);
        
        $this->createdOrderId = $order->id;

        $this->snapToken = $paymentService->getSnapToken($order);
        
        if ($this->snapToken) {
            $this->dispatch('open-snap', token: $this->snapToken);
        } else {
            session()->flash('error', 'Unable to initiate payment gateway. Please try again.');
        }
    }

    private function handleFreeAccess()
    {
        $this->saveParticipant('settlement');
        $this->success = true;
    }

    private function saveParticipant($paymentStatus)
    {
        $participant = new Participant([
            'name' => $this->name,
            'email' => $this->email,
            'whatsapp_number' => $this->phone,
            'age' => $this->age,
            'gender' => $this->gender,
            'domicile' => $this->domicile,
            'last_education' => $this->last_education,
            'education_status' => $this->education_status,
            'institution_name' => $this->education_status === 'Sedang Menempuh' ? $this->institution_name : null,
            'employment_status' => $this->employment_status,
            'current_job' => $this->employment_status === 'Sedang Bekerja' ? $this->current_job : null,
            'company' => $this->employment_status === 'Sedang Bekerja' ? $this->company : null,
            'background' => $this->background,
            'event_source' => $this->event_source,
            'privacy_consent' => true,
            'payment_status' => $paymentStatus,
            'amount_paid' => $this->purchasable->price ?? 0,
        ]);

        $this->purchasable->participants()->save($participant);
        return $participant;
    }

    public function render()
    {
        $jobOptions = \App\Models\Participant::jobOptions();
        $sourceOptions = \App\Models\Participant::sourceOptions();

        return view('livewire.checkout', [
            'jobOptions' => $jobOptions,
            'sourceOptions' => $sourceOptions,
        ]);
    }
}
