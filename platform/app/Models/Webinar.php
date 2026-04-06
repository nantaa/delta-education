<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Webinar extends Model
{
    /** @use HasFactory<\Database\Factories\WebinarFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'scheduled_at',
        'capacity',
        'zoom_link',
        'status',
        'poster',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];


    public function participants()
    {
        return $this->morphMany(Participant::class, 'participatable');
    }
}
