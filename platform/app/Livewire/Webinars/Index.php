<?php

namespace App\Livewire\Webinars;

use Livewire\Component;
use App\Models\Webinar;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    public function render()
    {
        $webinars = Webinar::where('status', 'published')
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return view('livewire.webinars.index', [
            'webinars' => $webinars,
        ]);
    }
}
