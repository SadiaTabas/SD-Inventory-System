<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type', // fixed, percent
        'value',
        'min_order_amount',
        'status',
    ];

    protected $casts = [
        'value'            => 'float',
        'min_order_amount' => 'float',
        'status'           => 'boolean',
    ];

    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->status || $subtotal < $this->min_order_amount) {
            return 0.00;
        }

        if ($this->type === 'percent') {
            $discount = $subtotal * ($this->value / 100);
        } else {
            $discount = $this->value;
        }

        return (float) min($discount, $subtotal);
    }
}
