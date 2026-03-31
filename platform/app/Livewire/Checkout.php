<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Webinar;
use App\Models\Product;
use App\Models\Order;
use App\Models\Registration;
use App\Services\PaymentGatewayService;
use App\Jobs\SendRegistrationConfirmation;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Checkout extends Component
{
    public Model $purchasable;
    public string $type;
    
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    
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
        ]);

        if ($this->type === 'webinar') {
            $exists = Registration::where('webinar_id', $this->purchasable->id)
                ->where('email', $this->email)
                ->exists();
                
            if ($exists) {
                $this->addError('email', 'This email is already registered for this webinar.');
                return;
            }
            
            if ($this->purchasable->registrations()->count() >= $this->purchasable->capacity) {
                $this->addError('capacity', 'Sorry, this webinar is fully booked.');
                return;
            }
        }

        if ($this->isFree) {
            $this->handleFreeAccess();
            return;
        }

        $order = Order::create([
            'customer_name' => $this->name,
            'customer_email' => $this->email,
            'customer_phone' => $this->phone,
            'total_amount' => $this->purchasable->price,
            'status' => 'pending',
            'payment_method' => 'midtrans_snap'
        ]);

        $order->items()->create([
            'productable_id' => $this->purchasable->id,
            'productable_type' => get_class($this->purchasable),
            'item_name' => $this->purchasable->title,
            'price' => $this->purchasable->price,
        ]);

        $this->snapToken = $paymentService->getSnapToken($order);
        
        if ($this->snapToken) {
            $this->dispatch('open-snap', token: $this->snapToken);
        } else {
            session()->flash('error', 'Unable to initiate payment gateway. Please try again.');
        }
    }

    private function handleFreeAccess()
    {
        if ($this->type === 'webinar') {
            $registration = $this->purchasable->registrations()->create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);
            SendRegistrationConfirmation::dispatch($registration);
        }
        
        $this->success = true;
    }

    public function render()
    {
        return view('livewire.checkout');
    }
}
