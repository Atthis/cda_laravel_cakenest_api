<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CupcakeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'imageSource' => $this->image,
            'title' => $this->name,
            'price' => $this->price_in_cents / 100,
            'quantity' => $this->quantity,
            'isAvailable' => $this->is_available,
            'isAsvertised' => $this->is_advertised,
        ];
    }
}