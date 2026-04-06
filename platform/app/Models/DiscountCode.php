<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'usage_limit',
        'used_count',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'valid_until'    => 'datetime',
        'is_active'      => 'boolean',
        'discount_value' => 'decimal:2',
    ];

    /**
     * Check if the code is currently valid.
     */
    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->valid_until && $this->valid_until->isPast()) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the final price after discount.
     */
    public function calculateDiscount(float $price): float
    {
        if (! $this->isValid()) {
            return $price;
        }

        if ($this->discount_type === 'percent') {
            $discountAmount = $price * ($this->discount_value / 100);
            return max(0, $price - $discountAmount);
        }

        // fixed discount
        return max(0, $price - $this->discount_value);
    }
}
