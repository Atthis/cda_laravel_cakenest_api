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
        return [
            'id' => $this->id,
            'customer' => new UserResource($this->whenLoaded('user')),
            'cupcakes' => PurchasedCupcakeResource::collection($this->whenLoaded('cupcakes'))
        ];
    }
}
