<?php

namespace App\Livewire\Webinars;

use Livewire\Component;
use App\Models\Webinar;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Show extends Component
{
    public Webinar $webinar;

    public function mount($slug)
    {
        $this->webinar = Webinar::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.webinars.show');
    }
}
