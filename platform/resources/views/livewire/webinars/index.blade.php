<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <h2 class="text-3xl font-bold mb-6 text-gray-800 dark:text-gray-200">Upcoming Webinars</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($webinars as $webinar)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">{{ $webinar->title }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 whitespace-normal line-clamp-3">
                        {{ $webinar->description }}
                    </p>
                    <div class="text-sm text-gray-500 dark:text-gray-300 mb-4">
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($webinar->scheduled_at)->format('F j, Y g:i A') }}
                    </div>
                    <a href="{{ route('webinars.show', $webinar->slug) }}" wire:navigate class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                        View & Register
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-500 py-10">
                No upcoming webinars at the moment. Please check back later!
            </div>
        @endforelse
    </div>
</div>
