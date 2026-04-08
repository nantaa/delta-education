<?php

namespace App\Livewire\Webinars;

use Livewire\Component;
use App\Models\Webinar;
use Livewire\Attributes\Layout;

use Livewire\WithPagination;

#[Layout('layouts.public')]
class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $webinars = Webinar::where('status', 'published')
            ->orderBy('scheduled_at', 'asc')
            ->paginate(9);

        return view('livewire.webinars.index', [
            'webinars' => $webinars,
        ]);
    }
}
