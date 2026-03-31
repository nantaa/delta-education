@push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endpush

<div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden mb-8">
        <div class="p-8">
            <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">Checkout: {{ $purchasable->title }}</h1>
            <p class="text-xl text-gray-700 dark:text-gray-300 mb-6">Total Amount: <span class="font-bold text-green-600 dark:text-green-400">Rp {{ number_format((float) $purchasable->price, 0, ',', '.') }}</span></p>

            @if($success)
                <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-6 rounded-lg text-center">
                    <h3 class="text-2xl font-bold mb-2">Success! 🎉</h3>
                    <p>Your action was completed successfully.</p>
                </div>
            @else
                <form wire:submit="processCheckout" class="space-y-4">
                    @if (session()->has('error'))
                        <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    @error('email')
                        <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ $message }}
                        </div>
                    @enderror

                    @error('capacity')
                        <div class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ $message }}
                        </div>
                    @enderror

                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Full Name</label>
                        <input wire:model="name" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-900 dark:border-gray-600 dark:text-white" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Email Address</label>
                        <input wire:model="email" type="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-900 dark:border-gray-600 dark:text-white" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">WhatsApp / Phone</label>
                        <input wire:model="phone" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-900 dark:border-gray-600 dark:text-white" required>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded transition duration-200" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                {{ $isFree ? 'Complete Free Registration' : 'Proceed to Payment (Rp ' . number_format((float) $purchasable->price, 0, ',', '.') . ')' }}
                            </span>
                            <span wire:loading>Processing...</span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@script
<script>
    $wire.on('open-snap', (data) => {
        window.snap.pay(data.token, {
            onSuccess: function(result){
                alert("Payment processing!");
                @this.set('success', true);
            },
            onPending: function(result){
                alert("Waiting your payment!");
            },
            onError: function(result){
                alert("Payment failed!");
            },
            onClose: function(){
                console.log('customer closed the popup without finishing the payment');
            }
        });
    });
</script>
@endscript
