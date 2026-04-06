@push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endpush

<div class="min-h-screen bg-[#FDFDFC] py-16 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-2xl">

        {{-- Breadcrumb --}}
        <nav class="mb-8 text-sm text-[#706f6c]">
            <a href="{{ route('home') }}" class="hover:text-[#1b1b18] transition-colors">Beranda</a>
            <span class="mx-2 text-[#e3e3e0]">/</span>
            <span class="text-[#1b1b18]">Pendaftaran</span>
        </nav>

        {{-- Title --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-[#1b1b18] tracking-tight">{{ $purchasable->title }}</h1>
            <p class="mt-1 text-sm text-[#706f6c]">Lengkapi formulir di bawah untuk menyelesaikan pendaftaran.</p>
        </div>

        @if($success)
            <div class="rounded-md border border-[#e3e3e0] p-10 text-center">
                <div class="mx-auto mb-4 flex h-10 w-10 items-center justify-center rounded-full border border-[#e3e3e0]">
                    <svg class="h-5 w-5 text-[#706f6c]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-[#1b1b18] mb-2">Pendaftaran berhasil!</h2>
                <p class="text-sm text-[#706f6c]">Terima kasih. Cek email dan WhatsApp Anda untuk informasi selanjutnya.</p>
                <a href="{{ route('home') }}" class="mt-6 inline-block text-xs text-[#706f6c] underline underline-offset-4 hover:text-[#1b1b18] transition-colors">
                    Kembali ke beranda
                </a>
            </div>
        @else

            @if (session()->has('error'))
                <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif
            @error('email') <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</div> @enderror
            @error('capacity') <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</div> @enderror

            <form wire:submit="processCheckout" class="space-y-8">

                {{-- Section: Data Pribadi --}}
                <fieldset>
                    <legend class="text-xs font-semibold text-[#706f6c] uppercase tracking-widest mb-4">Data Pribadi</legend>
                    <div class="rounded-md border border-[#e3e3e0] divide-y divide-[#e3e3e0]">

                        @php
                            $inputClass = 'block w-full bg-transparent px-4 py-3 text-sm text-[#1b1b18] placeholder-[#706f6c] focus:outline-none';
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-[#e3e3e0]">
                            <div>
                                <label class="block px-4 pt-2.5 text-[10px] font-semibold uppercase tracking-widest text-[#706f6c]">Nama Lengkap *</label>
                                <input wire:model="name" type="text" class="{{ $inputClass }}" placeholder="John Doe" required>
                            </div>
                            <div>
                                <label class="block px-4 pt-2.5 text-[10px] font-semibold uppercase tracking-widest text-[#706f6c]">Email *</label>
                                <input wire:model="email" type="email" class="{{ $inputClass }}" placeholder="john@email.com" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-[#e3e3e0]">
                            <div>
                                <label class="block px-4 pt-2.5 text-[10px] font-semibold uppercase tracking-widest text-[#706f6c]">WhatsApp *</label>
                                <input wire:model="phone" type="text" class="{{ $inputClass }}" placeholder="081234567890" required>
                            </div>
                            <div>
                                <label class="block px-4 pt-2.5 text-[10px] font-semibold uppercase tracking-widest text-[#706f6c]">Umur</label>
                                <input wire:model="age" type="number" class="{{ $inputClass }}" placeholder="25">
                            </div>
                            <div>
                                <label class="block px-4 pt-2.5 text-[10px] font-semibold uppercase tracking-widest text-[#706f6c]">Jenis Kelamin</label>
                                <select wire:model="gender" class="{{ $inputClass }}">
                                    <option value="">Pilih...</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block px-4 pt-2.5 text-[10px] font-semibold uppercase tracking-widest text-[#706f6c]">Domisili</label>
                            <input wire:model="domicile" type="text" class="{{ $inputClass }}" placeholder="Kota tempat tinggal">
                        </div>
                    </div>
                </fieldset>

                {{-- Section: Pendidikan --}}
                <fieldset x-data="{ eduStat: @entangle('education_status') }">
                    <legend class="text-xs font-semibold text-[#706f6c] uppercase tracking-widest mb-4">Pendidikan</legend>
                    <div class="rounded-md border border-[#e3e3e0] divide-y divide-[#e3e3e0]">
                        <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-[#e3e3e0]">
                            <div>
                                <label class="block px-4 pt-2.5 text-[10px] font-semibold uppercase tracking-widest text-[#706f6c]">Pendidikan Terakhir</label>
                                <select wire:model="last_education" class="{{ $inputClass }}">
                                    <option value="">Pilih...</option>
                                    <option>SMA/SMK</option><option>D3</option><option>D4/S1</option><option>S2</option><option>S3</option><option>Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block px-4 pt-2.5 text-[10px] font-semibold uppercase tracking-widest text-[#706f6c]">Status Pendidikan *</label>
                                <select x-model="eduStat" wire:model="education_status" class="{{ $inputClass }}">
                                    <option value="Tidak Sedang Menempuh">Tidak Sedang Menempuh</option>
                                    <option value="Sedang Menempuh">Sedang Menempuh</option>
                                </select>
                            </div>
                        </div>
                        <div x-show="eduStat === 'Sedang Menempuh'" x-collapse>
                            <label class="block px-4 pt-2.5 text-[10px] font-semibold uppercase tracking-widest text-[#706f6c]">Nama Instansi *</label>
                            <input wire:model="institution_name" type="text" class="{{ $inputClass }}" placeholder="Universitas / Sekolah">
                        </div>
                    </div>
                </fieldset>

                {{-- Section: Pekerjaan --}}
                <fieldset x-data="{ workStat: @entangle('employment_status') }">
                    <legend class="text-xs font-semibold text-[#706f6c] uppercase tracking-widest mb-4">Pekerjaan</legend>
                    <div class="rounded-md border border-[#e3e3e0] divide-y divide-[#e3e3e0]">
                        <div>
                            <label class="block px-4 pt-2.5 text-[10px] font-semibold uppercase tracking-widest text-[#706f6c]">Status Pekerjaan *</label>
                            <select x-model="workStat" wire:model="employment_status" class="{{ $inputClass }}">
                                <option value="Tidak Sedang Bekerja">Tidak Sedang Bekerja</option>
                                <option value="Sedang Bekerja">Sedang Bekerja</option>
                            </select>
                        </div>
                        <div x-show="workStat === 'Sedang Bekerja'" x-collapse>
                            <div class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-[#e3e3e0]">
                                <div>
                                    <label class="block px-4 pt-2.5 text-[10px] font-semibold uppercase tracking-widest text-[#706f6c]">Profesi *</label>
                                    <select wire:model="current_job" class="{{ $inputClass }}">
                                        <option value="">Pilih profesi...</option>
                                        @foreach($jobOptions as $val => $lbl)
                                            <option value="{{ $val }}">{{ $lbl }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block px-4 pt-2.5 text-[10px] font-semibold uppercase tracking-widest text-[#706f6c]">Perusahaan *</label>
                                    <input wire:model="company" type="text" class="{{ $inputClass }}" placeholder="Nama perusahaan/institusi">
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                {{-- Section: Tambahan --}}
                <fieldset>
                    <legend class="text-xs font-semibold text-[#706f6c] uppercase tracking-widest mb-4">Informasi Tambahan</legend>
                    <div class="rounded-md border border-[#e3e3e0] divide-y divide-[#e3e3e0]">
                        <div>
                            <label class="block px-4 pt-2.5 text-[10px] font-semibold uppercase tracking-widest text-[#706f6c]">Dari mana mengenal event ini?</label>
                            <select wire:model="event_source" class="{{ $inputClass }}">
                                <option value="">Pilih sumber...</option>
                                @foreach($sourceOptions as $val => $lbl)
                                    <option value="{{ $val }}">{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block px-4 pt-2.5 text-[10px] font-semibold uppercase tracking-widest text-[#706f6c]">Latar Belakang / Harapan</label>
                            <textarea wire:model="background" rows="3" class="{{ $inputClass }}" placeholder="Ceritakan motivasi Anda mengikuti event ini..."></textarea>
                        </div>
                    </div>
                </fieldset>

                {{-- Consent --}}
                <label class="flex items-start gap-3 cursor-pointer">
                    <input wire:model="privacy_consent" type="checkbox" required class="mt-0.5 h-4 w-4 rounded-sm border-[#e3e3e0] text-[#1b1b18]">
                    <span class="text-sm text-[#706f6c] leading-relaxed">
                        Saya menyetujui <a href="#" class="text-[#1b1b18] underline underline-offset-4">Kebijakan Privasi</a> dan mengizinkan Delta Education memproses data saya untuk keperluan event ini.
                    </span>
                </label>
                @error('privacy_consent') <p class="text-xs text-red-600 -mt-4">{{ $message }}</p> @enderror

                {{-- Submit --}}
                <div class="flex items-center justify-between pt-4 border-t border-[#e3e3e0]">
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-[#706f6c] mb-0.5">Total</p>
                        <p class="text-xl font-bold text-[#1b1b18]">{{ $isFree ? 'GRATIS' : 'Rp ' . number_format((float) $purchasable->price, 0, ',', '.') }}</p>
                    </div>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-sm bg-[#1b1b18] px-6 py-2.5 text-sm font-medium text-white hover:bg-black transition-colors disabled:opacity-50"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ $isFree ? 'Selesaikan Pendaftaran' : 'Lanjut ke Pembayaran' }}</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

@script
<script>
    $wire.on('open-snap', (data) => {
        window.snap.pay(data.token, {
            onSuccess: function(){ @this.set('success', true); },
            onPending: function(){ alert('Menunggu pembayaran...'); },
            onError: function(){ alert('Pembayaran gagal. Silakan coba lagi.'); },
            onClose: function(){ console.log('Midtrans popup closed'); }
        });
    });
</script>
@endscript
