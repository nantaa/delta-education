<div>
    {{-- Header --}}
    <div class="bg-black py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            <h1 class="text-2xl font-bold text-white tracking-tight mb-2">Pendaftaran Pelatihan</h1>
            <p class="text-gray-300">{{ $training->title }}</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8">

        @if ($success)
            <div class="bg-green-50 border border-green-200 rounded-md p-8 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 mb-6">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight mb-2">Pendaftaran Berhasil!</h2>
                <p class="text-gray-600 mb-6 max-w-sm mx-auto">Terima kasih telah mendaftar. Pembayaran Anda telah kami rekam.</p>
                
                @if($training->link_forwarder)
                    <div class="mb-8 p-4 bg-white border border-gray-200 rounded-md inline-block text-left w-full max-w-sm">
                        <p class="text-sm font-semibold text-gray-900 mb-2">Langkah Selanjutnya:</p>
                        <p class="text-xs text-gray-600 mb-4">Silakan isi formulir pendaftaran lengkap melalui link eksternal berikut untuk menyelesaikan proses Anda.</p>
                        <a href="{{ $training->link_forwarder }}" target="_blank" class="flex items-center justify-center gap-2 bg-black text-white px-4 py-2.5 rounded-sm font-medium hover:bg-gray-800 transition-colors w-full">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                            Isi Formulir Lengkap
                        </a>
                    </div>
                @endif

                <div class="mt-2">
                    <a href="{{ route('home') }}" class="inline-block bg-black text-white px-6 py-2.5 rounded-sm font-medium hover:bg-gray-800 transition-colors">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        @else

            {{-- Checkout Form --}}
            <form wire:submit="submit" class="bg-white border text-sm border-[#e3e3e0] rounded-md overflow-hidden shadow-sm">

                <div class="p-6 md:p-8 space-y-8">
                    
                    {{-- 1. Informasi Dasar --}}
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight mb-6 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-sm bg-black text-xs font-bold text-white">1</span>
                            Informasi Peserta
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap (Sesuai Sertifikat) <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.blur="name" class="block w-full border-gray-300 rounded-sm shadow-sm focus:border-black focus:ring-black text-sm" placeholder="Nama lengkap beserta gelar jika ada">
                                @error('name') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                                <input type="email" wire:model.blur="email" class="block w-full border-gray-300 rounded-sm shadow-sm focus:border-black focus:ring-black text-sm" placeholder="email@contoh.com">
                                @error('email') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor WhatsApp <span class="text-red-500">*</span></label>
                                <input type="tel" wire:model.blur="phone" class="block w-full border-gray-300 rounded-sm shadow-sm focus:border-black focus:ring-black text-sm" placeholder="081234567890">
                                @error('phone') <span class="text-red-500 text-xs mt-1.5 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="border-[#e3e3e0]">

                    {{-- 2. Diskon --}}
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight mb-6 flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-sm bg-black text-xs font-bold text-white">2</span>
                            Kode Diskon
                        </h2>

                        <div class="max-w-md">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Punya kode promo/diskon? (Opsional)</label>
                            <div class="flex gap-2">
                                <input type="text" wire:model="discount_code" class="block w-full uppercase border-gray-300 rounded-sm shadow-sm focus:border-black focus:ring-black text-sm" placeholder="Masukkan kode">
                                <button type="button" wire:click="applyDiscount" class="border border-[#19140035] bg-[#f5f5f3] px-4 rounded-sm text-sm font-medium hover:bg-gray-200 transition-colors">Terapkan</button>
                            </div>
                            
                            @if (session()->has('discount_success'))
                                <span class="text-green-600 text-xs mt-1.5 block font-medium">{{ session('discount_success') }}</span>
                            @endif
                            @if (session()->has('discount_error'))
                                <span class="text-red-500 text-xs mt-1.5 block">{{ session('discount_error') }}</span>
                            @endif
                        </div>
                    </div>

                    <hr class="border-[#e3e3e0]">
                    
                    {{-- Rekap --}}
                    <div class="bg-[#fcfcfb] -mx-6 md:-mx-8 -my-6 p-6 md:p-8 mt-4 border-t border-[#e3e3e0]">
                        @if (session()->has('error'))
                            <div class="mb-6 p-4 rounded-md bg-red-50 border border-red-200 flex items-start gap-3">
                                <svg class="h-5 w-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <h3 class="text-sm font-semibold text-red-800">Pembayaran Gagal Diinisiasi</h3>
                                    <p class="text-sm text-red-700 mt-1">{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="mb-6 flex gap-3 text-sm">
                            <input type="checkbox" wire:model="privacy_consent" id="privacy" class="mt-1 border-gray-300 rounded-sm text-black focus:ring-black">
                            <label for="privacy" class="text-gray-600 leading-relaxed cursor-pointer">
                                Saya bersedia dan memberikan izin penuh atas pengolahan data pribadi saya oleh pihak Delta Education...
                            </label>
                        </div>
                        @error('privacy_consent') <span class="text-red-500 text-xs block mb-4 -mt-2">{{ $message }}</span> @enderror

                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pt-4 border-t border-[#e3e3e0]">
                            <div>
                                <span class="text-sm text-gray-500 font-medium block mb-1">Total Pembayaran</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl font-bold tracking-tight text-gray-900">
                                        {{ $effectivePrice > 0 ? 'Rp ' . number_format($effectivePrice, 0, ',', '.') : 'Gratis' }}
                                    </span>
                                    @if($appliedDiscountId && $originalPrice > 0 && $effectivePrice < $originalPrice)
                                        <span class="text-sm text-gray-400 line-through">Rp {{ number_format($originalPrice, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>

                            <button type="submit" 
                                    class="bg-black text-white px-8 py-3 rounded-sm font-medium hover:bg-gray-800 focus:ring-2 focus:ring-offset-2 focus:ring-black transition-all flex items-center justify-center gap-2 group w-full md:w-auto"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-75 cursor-not-allowed">
                                <span wire:loading.remove>Lanjutkan Pembayaran</span>
                                <span wire:loading>Memproses...</span>
                                <svg wire:loading.remove class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </div>

                </div>
            </form>

        @endif

    </div>

    {{-- Midtrans Snap Script --}}
    @if(config('services.midtrans.is_production'))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @endif

    @script
    <script>
        $wire.on('open-snap', (data) => {
            let token = Array.isArray(data) ? data[0].token : data.token;
            window.snap.pay(token, {
                onSuccess: function() { @this.call('handlePaymentSuccess'); },
                onPending: function() { window.location.href = '/order/' + @this.createdOrderId; },
                onError: function() { alert('Midtrans membatalkan pembayaran Anda.'); },
                onClose: function() { window.location.href = '/order/' + @this.createdOrderId; }
            });
        });
    </script>
    @endscript
</div>
