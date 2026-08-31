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
        'title',
        'contact_person_name',
        'contact_phone',
        'address',
        'city',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}