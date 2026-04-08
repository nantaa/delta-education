<div class="py-20 px-4 sm:px-6 lg:px-8 bg-[#FDFDFC] min-h-screen">
    <div class="mx-auto max-w-5xl">

        <div class="mb-12">
            <h1 class="text-2xl font-bold text-[#1b1b18] tracking-tight mb-2">Semua Webinar</h1>
            <p class="text-[#706f6c] text-sm">Jelajahi jadwal webinar dan daftarkan dirimu sebelum tempat penuh.</p>
        </div>

        @if($webinars->isEmpty())
            <div class="rounded-md border border-[#e3e3e0] px-8 py-16 text-center text-[#706f6c] text-sm">
                Belum ada webinar terjadwal. Pantau terus halaman ini.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($webinars as $webinar)
                <div class="flex flex-col rounded-md border border-[#e3e3e0] bg-white overflow-hidden hover:border-[#1915014a] hover:shadow-sm transition-all duration-150">

                    {{-- Poster Thumbnail --}}
                    <div class="aspect-video bg-[#f5f5f3] flex items-center justify-center overflow-hidden">
                        @if($webinar->poster)
                            <img src="{{ Storage::url($webinar->poster) }}"
                                 alt="Poster {{ $webinar->title }}"
                                 class="w-full h-full object-cover">
                        @else
                            <svg class="h-8 w-8 text-[#e3e3e0]" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 8.25V6a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 6v12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18.75V8.25zM13.5 10.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                            </svg>
                        @endif
                    </div>

                    <div class="flex flex-col flex-1 p-6">
                        <div class="mb-4">
                            <span class="inline-block rounded-sm border border-[#19140035] px-2.5 py-0.5 text-[11px] font-medium text-[#706f6c]">
                                {{ \Carbon\Carbon::parse($webinar->scheduled_at)->translatedFormat('j M Y · H:i') }}
                            </span>
                        </div>

                        <h2 class="text-base font-semibold text-[#1b1b18] leading-snug mb-2 flex-1">{{ $webinar->title }}</h2>

                        <p class="text-sm text-[#706f6c] line-clamp-2 mb-6 leading-relaxed">{{ $webinar->description }}</p>

                        <div class="flex items-center justify-between pt-4 border-t border-[#e3e3e0]">
                            <span class="text-sm font-semibold text-[#1b1b18]">
                                {{ $webinar->price > 0 ? 'Rp ' . number_format($webinar->price, 0, ',', '.') : 'Gratis' }}
                            </span>
                            <a href="{{ route('webinars.show', $webinar->slug) }}"
                               class="inline-flex items-center gap-1 text-xs font-medium text-[#706f6c] underline underline-offset-4 hover:text-[#1b1b18] transition-colors">
                                Detail & Daftar
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                    <div class="col-span-full text-center text-[#706f6c] text-sm py-12">
                        Belum ada webinar terjadwal.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $webinars->links() }}
            </div>
        @endif
    </div>
</div>
