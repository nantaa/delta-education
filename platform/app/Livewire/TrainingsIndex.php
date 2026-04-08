<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Training;
use Livewire\Attributes\Layout;

use Livewire\WithPagination;

#[Layout('layouts.public')]
class TrainingsIndex extends Component
{
    use WithPagination;

    public function render()
    {
        $trainings = Training::where('status', 'published')
            ->orderBy('scheduled_at', 'asc')
            ->paginate(9);

        return view('livewire.trainings-index', [
            'trainings' => $trainings,
        ]);
    }
}
