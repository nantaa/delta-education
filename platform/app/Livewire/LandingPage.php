<?php

namespace App\Livewire;

use App\Models\Webinar;
use Illuminate\Support\Collection;
use Livewire\Component;

class LandingPage extends Component
{
    public Collection $webinars;
    public Collection $trainings;

    public function mount(): void
    {
        $this->webinars = Webinar::query()
            ->where('status', 'published')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->take(6)
            ->get();

        $this->trainings = \App\Models\Training::query()
            ->where('status', 'published')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->take(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.landing-page')
            ->layout('layouts.public');
    }
}
