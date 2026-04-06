<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'scheduled_at',
        'capacity',
        'poster',
        'status',
        'location',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'price'        => 'decimal:2',
    ];

    public function participants()
    {
        return $this->morphMany(Participant::class, 'participatable');
    }
}
