<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Training;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')]
class TrainingsIndex extends Component
{
    public function render()
    {
        $trainings = Training::where('status', 'published')
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return view('livewire.trainings-index', [
            'trainings' => $trainings,
        ]);
    }
}
