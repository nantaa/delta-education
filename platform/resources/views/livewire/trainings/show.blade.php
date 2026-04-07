<div class="py-20 px-4 sm:px-6 lg:px-8 bg-[#FDFDFC] min-h-screen">
    <div class="mx-auto max-w-3xl">

        {{-- Breadcrumb --}}
        <nav class="mb-8 text-sm text-[#706f6c]">
            <a href="{{ route('home') }}" class="hover:text-[#1b1b18] transition-colors">Beranda</a>
            <span class="mx-2 text-[#e3e3e0]">/</span>
            <span class="text-[#1b1b18]">Pelatihan K3</span>
            <span class="mx-2 text-[#e3e3e0]">/</span>
            <span class="text-[#1b1b18]">{{ $training->title }}</span>
        </nav>

        {{-- Poster Image (Vertical Ratio) --}}
        <div class="mb-8 rounded-md border border-[#e3e3e0] overflow-hidden aspect-[3/4] bg-[#f5f5f3] flex items-center justify-center">
            @if($training->poster)
                <img src="{{ Storage::url($training->poster) }}"
                     alt="Poster {{ $training->title }}"
                     class="w-full h-full object-cover">
            @else
                <div class="flex flex-col items-center gap-2 text-[#706f6c]">
                    <svg class="h-10 w-10 opacity-30" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 8.25V6a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 6v12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18.75V8.25zM13.5 10.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                    </svg>
                    <span class="text-xs">Poster belum tersedia</span>
                </div>
            @endif
        </div>

        {{-- Date & Location badge --}}
        <div class="mb-4 flex flex-wrap gap-2">
            <span class="inline-block rounded-sm border border-[#19140035] px-2.5 py-0.5 text-[11px] font-medium text-[#706f6c]">
                {{ \Carbon\Carbon::parse($training->scheduled_at)->translatedFormat('l, j F Y · H:i') }} WIB
            </span>
            @if($training->location)
                <span class="inline-block rounded-sm border border-[#19140035] px-2.5 py-0.5 text-[11px] font-medium text-[#706f6c]">
                    {{ $training->location }}
                </span>
            @endif
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold text-[#1b1b18] leading-snug tracking-tight mb-4">{{ $training->title }}</h1>

        {{-- Meta row --}}
        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 mb-8 text-sm text-[#706f6c]">
            <span class="flex items-center gap-1.5">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                Sisa {{ max(0, $training->capacity - $training->participants()->count()) }} / {{ $training->capacity }} tempat
            </span>
            <span class="flex items-center gap-1.5 font-semibold text-[#1b1b18]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75"/></svg>
                {{ $training->price > 0 ? 'Rp ' . number_format($training->price, 0, ',', '.') : 'Gratis' }}
            </span>
        </div>

        <div class="border-t border-[#e3e3e0] mb-8"></div>

        {{-- Description --}}
        <div class="prose prose-sm max-w-none text-[#706f6c] leading-relaxed mb-12">
            {!! nl2br(e($training->description)) !!}
        </div>

        {{-- CTA --}}
        <div class="rounded-md border border-[#e3e3e0] p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-[#1b1b18] mb-0.5">Siap bergabung?</p>
                <p class="text-xs text-[#706f6c]">Amankan tempat Anda sebelum pendaftaran ditutup.</p>
            </div>
            
            @if($training->status !== 'closed')
                <a href="{{ route('checkout.training', ['slug' => $training->slug]) }}"
                   class="inline-flex items-center gap-2 rounded-md bg-[#1b1b18] px-5 py-2.5 text-sm font-medium text-white hover:bg-black transition-colors whitespace-nowrap">
                    Daftar Sekarang
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <button disabled class="inline-flex items-center gap-2 rounded-md bg-gray-300 px-5 py-2.5 text-sm font-medium text-gray-500 cursor-not-allowed whitespace-nowrap">
                    Pendaftaran Ditutup
                </button>
            @endif
        </div>
    </div>
</div>
