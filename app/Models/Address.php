<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'address_line',
        'area',
        'city',
        'district',
        'postal_code',
        'delivery_instructions',
        'type', // home, office, other
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors for backward compatibility
    public function getContactPersonNameAttribute()
    {
        return $this->full_name;
    }

    public function setContactPersonNameAttribute($value)
    {
        $this->attributes['full_name'] = $value;
    }

    public function getContactPhoneAttribute()
    {
        return $this->phone;
    }

    public function setContactPhoneAttribute($value)
    {
        $this->attributes['phone'] = $value;
    }

    public function getAddressAttribute()
    {
        return $this->address_line;
    }

    public function setAddressAttribute($value)
    {
        $this->attributes['address_line'] = $value;
    }

    public function getTitleAttribute()
    {
        return ucfirst($this->type ?? 'Home');
    }

    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line,
            $this->area,
            $this->city,
            $this->district,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }
}
