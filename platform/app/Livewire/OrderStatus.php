<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use App\Models\Participant;
use App\Services\PaymentGatewayService;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class OrderStatus extends Component
{
    public Order $order;
    public $participant;
    public $purchasable;
    public ?string $snapToken = null;

    public function mount($order_id)
    {
        $this->order = Order::findOrFail($order_id);
        $this->participant = Participant::where('transaction_id', $this->order->id)->first();
        if ($this->participant) {
            $this->purchasable = $this->participant->participatable;
        }
    }

    public function pay(PaymentGatewayService $paymentService)
    {
        $this->snapToken = $paymentService->getSnapToken($this->order);
        if ($this->snapToken) {
            $this->dispatch('open-snap', token: $this->snapToken);
        } else {
            session()->flash('error', 'Gagal menginisiasi pembayaran. Silakan cek konfigurasi Midtrans.');
        }
    }

    public function checkStatus()
    {
        $this->order->refresh();
        // Since Livewire reactive traits naturally re-render when models update,
        // simply refreshing the $this->order will automatically swap the UI state
        // if the background webhook transitioned it to 'paid'.
    }

    public function handlePaymentSuccess()
    {
        // Optimistic UI update. Webhook will securely finalize it.
        $this->order->refresh();
        $this->order->status = 'paid';
    }

    public function render()
    {
        return view('livewire.order-status');
    }
}
