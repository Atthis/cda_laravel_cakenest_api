<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupcake extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'image',
        'quantity',
        'is_available',
        'is_advertised',
        'price_in_cents',
        'price'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, mixed $attributes) => $attributes['price_in_cents'] / 100,
            set: fn (mixed $value) => [
                'price_in_cents' => floor($value * 100)
                ]
        );
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class)->withPivot(['quantity', 'price']);
    }
}
