<div wire:poll.3s="checkStatus">
    <div class="py-16 px-4 bg-[#FDFDFC] min-h-screen">
        <div class="mx-auto max-w-2xl bg-white border border-[#e3e3e0] rounded-md p-8 text-center shadow-sm">
            @if(in_array($order->status, ['paid', 'settlement']))
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100 mb-6">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight mb-2">Pendaftaran Berhasil!</h2>
                <p class="text-gray-600 mb-6 max-w-sm mx-auto">Terima kasih telah menyelesaikan pendaftaran. Pembayaran Anda telah kami verifikasi.</p>
                
                @if($purchasable)
                    @if(isset($purchasable->link_forwarder) && $purchasable->link_forwarder)
                        <div class="mb-8 p-4 bg-white border border-gray-200 rounded-md inline-block text-left w-full max-w-sm">
                            <p class="text-sm font-semibold text-gray-900 mb-2">Langkah Selanjutnya:</p>
                            <p class="text-xs text-gray-600 mb-4">Silakan isi formulir pendaftaran lengkap melalui link eksternal berikut untuk menyelesaikan proses Anda.</p>
                            <a href="{{ $purchasable->link_forwarder }}" target="_blank" class="flex items-center justify-center gap-2 bg-black text-white px-4 py-2.5 rounded-sm font-medium hover:bg-gray-800 transition-colors w-full">
                                Isi Formulir Lengkap
                            </a>
                        </div>
                    @endif

                    @if(isset($purchasable->zoom_link) && $purchasable->zoom_link)
                         <div class="mb-4">
                            <a href="{{ $purchasable->zoom_link }}" target="_blank" class="inline-block bg-black text-white px-6 py-2.5 rounded-sm font-medium">Buka Link Kegiatan</a>
                        </div>
                    @endif
                @endif
                
                <div class="mt-2">
                    <a href="{{ route('home') }}" class="inline-block text-sm text-[#706f6c] hover:text-[#1b1b18] underline">Kembali ke Beranda</a>
                </div>
            @else
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-yellow-100 mb-6">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight mb-2">Menunggu Pembayaran</h2>
                <p class="text-gray-600 mb-6">Pendaftaran terekam. Segera selesaikan pembayaran untuk memvalidasi kursi Anda. Jika terputus atau tertutup, klik tombol di bawah.</p>
                
                @if (session()->has('error'))
                    <div class="mb-4 p-4 rounded-md bg-red-50 border border-red-200">
                        <p class="text-sm text-red-600">{{ session('error') }}</p>
                    </div>
                @endif
                
                <div class="flex gap-4 items-center gap-x-4 justify-center">
                    <button wire:click="pay" wire:loading.attr="disabled" class="bg-black text-white px-6 py-2.5 rounded-sm font-medium mb-4 flex items-center justify-center gap-2 transition-colors hover:bg-gray-800">
                        <span wire:loading.remove>Lanjutkan Pembayaran</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                </div>

                <div class="mt-4">
                    <a href="{{ route('home') }}" class="inline-block text-sm text-[#706f6c] hover:text-[#1b1b18] underline">Kembali Ke Halaman Utama</a>
                </div>
            @endif
        </div>
    </div>
    
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
                onPending: function() { alert('Status masih tertunda. Silakan instruksi pada Snap.'); },
                onError: function() { alert("Pembayaran gagal!"); },
                onClose: function() { console.log('Midtrans Popup Closed.'); }
            });
        });
    </script>
    @endscript
</div>
