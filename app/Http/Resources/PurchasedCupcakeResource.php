<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchasedCupcakeResource extends JsonResource
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
            'price' => $this->pivot->price / 100,
            'quantity' => $this->pivot->quantity,
        ];
    }
}
