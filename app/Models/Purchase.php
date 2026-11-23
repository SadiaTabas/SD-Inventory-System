<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number',
        'supplier_id',
        'total_amount',
        'status', // draft, ordered, partially_received, received, cancelled
        'purchase_date',
        'received_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'total_amount'  => 'float',
        'purchase_date' => 'date',
        'received_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
