<div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden mb-8">
        <div class="p-8">
            <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">{{ $webinar->title }}</h1>
            
            <div class="flex items-center text-gray-600 dark:text-gray-300 mb-6 space-x-4">
                <div><strong>Schedule:</strong> {{ \Carbon\Carbon::parse($webinar->scheduled_at)->format('F j, Y g:i A') }}</div>
                <div><strong>Available Seats:</strong> {{ max(0, $webinar->capacity - $webinar->registrations()->count()) }} / {{ $webinar->capacity }}</div>
                <div><strong>Price:</strong> {{ $webinar->price > 0 ? 'Rp ' . number_format($webinar->price, 0, ',', '.') : 'Free' }}</div>
            </div>
            
            <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 mb-8">
                {!! nl2br(e($webinar->description)) !!}
            </div>

            <div class="mt-8">
                <a href="{{ route('checkout', ['type' => 'webinar', 'slug' => $webinar->slug]) }}" wire:navigate class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded shadow text-lg transition duration-200">
                    {{ $webinar->price > 0 ? 'Buy Ticket' : 'Register Now' }}
                </a>
            </div>
        </div>
    </div>
</div>
