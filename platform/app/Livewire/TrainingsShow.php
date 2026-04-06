<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Training;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class TrainingsShow extends Component
{
    public Training $training;

    public function mount($slug)
    {
        $this->training = Training::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.trainings.show');
    }
}
