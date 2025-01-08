<?php

namespace App\Http\Resources;

use App\Models\Coupon;
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
        $coupon = Coupon::find($this->coupon_id);
        $purchase_total = 0;
        $cupcakes = PurchasedCupcakeResource::collection($this->whenLoaded('cupcakes'));

        foreach ($cupcakes as $cupcake) {
            $cupcake_total = floor($cupcake->pivot->price) * $cupcake->pivot->quantity;
            $purchase_total += $cupcake_total;
        }

        $purchase_total_with_coupon = floor($purchase_total * (1 - $coupon->value / 100)) / 100;

        return [
            'id' => $this->id,
            'customer' => new UserResource($this->whenLoaded('user')),
            'cupcakes' => $cupcakes,
            'purchase_total' => $purchase_total / 100,
            'coupon'=> [
                'id'=> $coupon->id,
                'code'=> $coupon->code,
                'value'=> $coupon->value
            ],
            'purchase_total_with_coupon'=> $purchase_total_with_coupon
        ];
    }
}
