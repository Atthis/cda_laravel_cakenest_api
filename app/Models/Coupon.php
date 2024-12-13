<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'value',
        'start_date',
        'expire_date'
    ];

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class);
    }
}
