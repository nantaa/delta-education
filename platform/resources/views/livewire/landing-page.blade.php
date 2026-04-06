@section('title', 'Delta Education – Webinar & Pelatihan Online Terbaik')
@section('meta_description', 'Tingkatkan kompetensi bersama para praktisi berpengalaman. Ikuti webinar dan pelatihan online dari Delta Education.')

<div>

{{-- ══════════════════════════════════════
     HERO
══════════════════════════════════════ --}}
<section class="pt-24 pb-20 px-4 sm:px-6 lg:px-8 bg-[#FDFDFC]">
    <div class="mx-auto max-w-5xl text-center">

        <a href="{{ route('webinars.index') }}" class="inline-flex items-center gap-1.5 rounded-full border border-[#19140035] px-3 py-1 text-xs font-medium text-[#706f6c] hover:border-[#1915014a] transition-colors mb-8">
            Webinar Series 2025
            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>

        <h1 class="text-4xl sm:text-5xl lg:text-[3.5rem] font-bold tracking-tight text-[#1b1b18] leading-[1.1] mb-6">
            Belajar dari para praktisi.<br>Tingkatkan kompetensimu.
        </h1>

        <p class="mx-auto max-w-xl text-base sm:text-lg text-[#706f6c] leading-relaxed mb-10">
            Delta Education menghadirkan webinar interaktif yang dirancang bersama profesional industri — dari data, desain, hingga pengembangan produk digital.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('webinars.index') }}"
               class="inline-flex items-center gap-2 rounded-md bg-[#1b1b18] px-5 py-2.5 text-sm font-medium text-white hover:bg-black transition-colors">
                Lihat Webinar
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
            <a href="#webinars"
               class="inline-flex items-center gap-2 rounded-md border border-[#19140035] px-5 py-2.5 text-sm font-medium text-[#1b1b18] hover:border-[#1915014a] transition-colors">
                Jadwal Mendatang
            </a>
        </div>
    </div>
</section>

<div class="border-t border-[#e3e3e0]"></div>

{{-- ══════════════════════════════════════
     STATS BAR
══════════════════════════════════════ --}}
<section class="py-10 px-4 bg-[#FDFDFC]">
    <dl class="mx-auto max-w-5xl grid grid-cols-2 md:grid-cols-4 gap-px bg-[#e3e3e0]">
        @php
            $stats = [
                ['Peserta','1.200+'],
                ['Webinar','24+'],
                ['Mitra','8+'],
                ['Nilai Kepuasan','4.9/5'],
            ];
        @endphp
        @foreach($stats as $stat)
        <div class="bg-[#FDFDFC] px-8 py-8 text-center">
            <dt class="text-sm text-[#706f6c] mb-1">{{ $stat[0] }}</dt>
            <dd class="text-2xl font-bold text-[#1b1b18] tracking-tight">{{ $stat[1] }}</dd>
        </div>
        @endforeach
    </dl>
</section>

<div class="border-t border-[#e3e3e0]"></div>

{{-- ══════════════════════════════════════
     WEBINAR LISTING
══════════════════════════════════════ --}}
<section id="webinars" class="py-20 px-4 sm:px-6 lg:px-8 bg-[#FDFDFC]">
    <div class="mx-auto max-w-5xl">

        <div class="mb-12">
            <h2 class="text-2xl font-bold text-[#1b1b18] tracking-tight mb-2">Webinar Mendatang</h2>
            <p class="text-[#706f6c] text-sm">Pilih webinar dan daftarkan dirimu sekarang sebelum tempat penuh.</p>
        </div>

        @if($webinars->isEmpty())
            <div class="rounded-md border border-[#e3e3e0] px-8 py-16 text-center text-[#706f6c]">
                Belum ada webinar terjadwal. Pantau terus halaman ini.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($webinars as $webinar)
                <div class="group flex flex-col rounded-md border border-[#e3e3e0] bg-white overflow-hidden hover:border-[#1915014a] hover:shadow-sm transition-all duration-150">

                    {{-- Poster --}}
                    <div class="aspect-video bg-[#f5f5f3] flex items-center justify-center overflow-hidden">
                        @if($webinar->poster)
                            <img src="{{ Storage::url($webinar->poster) }}"
                                 alt="Poster {{ $webinar->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <svg class="h-8 w-8 text-[#d4d4d0]" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 8.25V6a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 6v12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18.75V8.25zM13.5 10.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                            </svg>
                        @endif
                    </div>

                    <div class="flex flex-col flex-1 p-6">
                        {{-- Date badge --}}
                        <div class="mb-4">
                            <span class="inline-block rounded-sm border border-[#19140035] px-2.5 py-0.5 text-[11px] font-medium text-[#706f6c]">
                                {{ \Carbon\Carbon::parse($webinar->scheduled_at)->translatedFormat('j M Y') }}
                            </span>
                        </div>

                        <h3 class="text-base font-semibold text-[#1b1b18] leading-snug mb-2 flex-1">{{ $webinar->title }}</h3>

                        <p class="text-sm text-[#706f6c] line-clamp-2 mb-6 leading-relaxed">{{ $webinar->description }}</p>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-[#1b1b18]">
                                {{ $webinar->price > 0 ? 'Rp ' . number_format($webinar->price, 0, ',', '.') : 'Gratis' }}
                            </span>
                            <a href="{{ route('checkout', ['type' => 'webinar', 'slug' => $webinar->slug]) }}"
                               class="inline-flex items-center gap-1.5 rounded-sm border border-[#19140035] bg-white px-3.5 py-1.5 text-xs font-medium text-[#1b1b18] hover:border-black hover:bg-black hover:text-white transition-all duration-150">
                                Daftar Sekarang
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('webinars.index') }}" class="text-sm text-[#706f6c] underline underline-offset-4 hover:text-[#1b1b18] transition-colors">
                    Lihat semua webinar →
                </a>
            </div>
        @endif
    </div>
</section>

<div class="border-t border-[#e3e3e0]"></div>

{{-- ══════════════════════════════════════
     PELATIHAN K3 SECTION
══════════════════════════════════════ --}}
<section class="py-20 px-4 sm:px-6 lg:px-8 bg-[#f5f5f3]">
    <div class="mx-auto max-w-5xl">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
            <div>
                <h2 class="text-2xl font-bold text-[#1b1b18] tracking-tight mb-2">Pelatihan K3 Terdekat</h2>
                <p class="text-[#706f6c] text-sm">Amankan kursi untuk sertifikasi kompetensi Anda.</p>
            </div>
            {{-- Optional: Link to trainings.index if created --}}
        </div>

        @if($trainings->isEmpty())
            <div class="rounded-md border border-[#e3e3e0] border-dashed p-12 text-center bg-[#FDFDFC]">
                <p class="text-sm text-[#706f6c]">Belum ada jadwal pelatihan saat ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($trainings as $training)
                <div class="group flex flex-col rounded-md border border-[#e3e3e0] bg-[#FDFDFC] overflow-hidden hover:border-black hover:shadow-sm transition-all duration-150">

                    {{-- Poster --}}
                    <div class="aspect-video bg-[#eaeaea] flex items-center justify-center overflow-hidden">
                        @if($training->poster)
                            <img src="{{ Storage::url($training->poster) }}"
                                 alt="Poster {{ $training->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <svg class="h-8 w-8 text-[#d4d4d0]" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 8.25V6a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 6v12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18.75V8.25zM13.5 10.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                            </svg>
                        @endif
                    </div>

                    <div class="flex flex-col flex-1 p-6">
                        {{-- Date & Location badge --}}
                        <div class="mb-4 flex flex-wrap gap-2">
                            <span class="inline-block rounded-sm border border-[#19140035] px-2.5 py-0.5 text-[11px] font-medium text-[#706f6c]">
                                {{ \Carbon\Carbon::parse($training->scheduled_at)->translatedFormat('j M Y') }}
                            </span>
                            @if($training->location)
                            <span class="inline-block rounded-sm border border-[#19140035] px-2.5 py-0.5 text-[11px] font-medium text-[#706f6c]">
                                {{ Str::limit($training->location, 20) }}
                            </span>
                            @endif
                        </div>

                        <a href="{{ route('trainings.show', ['slug' => $training->slug]) }}" class="text-base font-semibold text-[#1b1b18] leading-snug mb-2 flex-1 hover:underline underline-offset-2">
                            {{ $training->title }}
                        </a>

                        <p class="text-sm text-[#706f6c] line-clamp-2 mb-6 leading-relaxed">{{ $training->description }}</p>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-[#1b1b18]">
                                {{ $training->price > 0 ? 'Rp ' . number_format($training->price, 0, ',', '.') : 'Gratis' }}
                            </span>
                            <a href="{{ route('trainings.show', ['slug' => $training->slug]) }}"
                               class="inline-flex items-center gap-1.5 rounded-sm border border-[#19140035] bg-transparent px-3.5 py-1.5 text-xs font-medium text-[#1b1b18] hover:border-black hover:bg-black hover:text-white transition-all duration-150">
                                Detail Pelatihan
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<div class="border-t border-[#e3e3e0]"></div>

{{-- ══════════════════════════════════════
     WHY DELTA
══════════════════════════════════════ --}}
<section class="py-20 px-4 sm:px-6 lg:px-8 bg-[#FDFDFC]">
    <div class="mx-auto max-w-5xl">
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-[#1b1b18] tracking-tight mb-2">Mengapa Delta Education?</h2>
            <p class="text-[#706f6c] text-sm">Kami merancang pengalaman belajar yang relevan, praktis, dan berdampak.</p>
        </div>

        @php
            $features = [
                ['Kurator Terpilih','Setiap narasumber adalah praktisi aktif di bidangnya, bukan sekadar akademisi.'],
                ['Materi Praktis','Fokus pada skill yang langsung dapat diterapkan di dunia kerja.'],
                ['Komunitas Aktif','Bergabung dengan ribuan peserta yang saling mendukung pasca webinar.'],
                ['Rekaman Tersedia','Peserta mendapat akses rekaman webinar untuk ditonton ulang kapan saja.'],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-px bg-[#e3e3e0]">
            @foreach($features as $f)
            <div class="bg-[#FDFDFC] p-8">
                <div class="mb-3 flex h-8 w-8 items-center justify-center rounded-sm border border-[#e3e3e0]">
                    <svg class="h-4 w-4 text-[#706f6c]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-[#1b1b18] mb-1.5">{{ $f[0] }}</h3>
                <p class="text-sm text-[#706f6c] leading-relaxed">{{ $f[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<div class="border-t border-[#e3e3e0]"></div>

{{-- ══════════════════════════════════════
     BOTTOM CTA
══════════════════════════════════════ --}}
<section class="py-20 px-4 sm:px-6 lg:px-8 bg-[#FDFDFC]">
    <div class="mx-auto max-w-5xl flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
        <div>
            <h2 class="text-2xl font-bold text-[#1b1b18] tracking-tight mb-2">Siap memulai perjalananmu?</h2>
            <p class="text-[#706f6c] text-sm max-w-md">Lihat jadwal webinar terbaru dan amankan tempat sebelum kehabisan.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('webinars.index') }}"
               class="inline-flex items-center gap-2 rounded-md bg-[#1b1b18] px-5 py-2.5 text-sm font-medium text-white hover:bg-black transition-colors">
                Jelajahi Webinar
            </a>
            @guest
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 rounded-md border border-[#19140035] px-5 py-2.5 text-sm font-medium text-[#1b1b18] hover:border-[#1915014a] transition-colors">
                Masuk
            </a>
            @endguest
        </div>
    </div>
</section>

</div>
