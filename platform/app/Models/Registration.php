<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    /** @use HasFactory<\Database\Factories\RegistrationFactory> */
    use HasFactory;

    protected $fillable = [
        'webinar_id',
        'name',
        'email',
        'phone',
        'organization',
        'attended',
    ];

    protected $casts = [
        'attended' => 'boolean',
    ];

    public function webinar()
    {
        return $this->belongsTo(Webinar::class);
    }
}
