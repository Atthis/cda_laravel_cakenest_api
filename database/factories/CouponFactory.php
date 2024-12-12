<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $couponValue = random_int(1, 8) * 10;
        $carbonDate = Carbon::now();
        $startDate = $carbonDate->isoFormat('YYYY-MM-DDThh:mm:ss');
        $expireDate = $carbonDate->addDays(5)->isoFormat('YYYY-MM-DDThh:mm:ss');

        return [
            'value'=> $couponValue,
            'code'=> fake()->word() . strval($couponValue),
            'start_date'=> $startDate,
            'expire_date'=> $expireDate,
        ];
    }
}
