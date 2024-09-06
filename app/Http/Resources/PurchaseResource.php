<?php

namespace App\Http\Resources;

use App\Models\Cupcake;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $purchase_total = 0;
        $cupcakes = PurchasedCupcakeResource::collection($this->whenLoaded('cupcakes'));

        foreach ($cupcakes as $cupcake) {
            $cupcake_total = floor($cupcake->pivot->price) * $cupcake->pivot->quantity;
            $purchase_total += $cupcake_total;
        }

        return [
            'id' => $this->id,
            'customer' => new UserResource($this->whenLoaded('user')),
            'cupcakes' => $cupcakes,
            'purchase_total' => $purchase_total / 100
        ];
    }
}
