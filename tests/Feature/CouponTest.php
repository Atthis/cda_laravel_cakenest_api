<?php

// Coupon tests
// - i can create a coupon only if i'm an administator
// - i can associate a coupon to a purchase if i'm authenticated
// - when a coupon is associated to a purchase, a link is created between the coupon and the purchase
// - when the coupon is added to the purchase, the purchase total is reduce depending on the coupon value
// - if the coupon doesn't exist, it cannot apply
// - if the coupon is expired, it cannot apply

use App\Models\Coupon;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;


// ******************
// Coupon Creation tests
// ******************
test('an unauthendicated user cannot create a coupon', function()
{
  $coupon = [
    'name'=> 'reduc20',
    'start_date'=> "2024-12-10",
    'expire_date'=> "2024-12-15",
    'value'=> 20
  ];

  postJson(route('coupon.create'), $coupon)
      ->assertUnauthorized();

  // Control if cupcake has not been created
  expect(Coupon::count())
      ->toEqual(0);
});

test('a non-admin user cannot create a coupon', function()
{
  $coupon = [
    'name'=> 'reduc20',
    'start_date'=> "2024-12-10",
    'expire_date'=> "2024-12-15",
    'value'=> 20
  ];

  /**
   * @var user
   */
  $user = User::factory()->create();

  actingAs($user)
    ->postJson(route('coupon.create'), $coupon)
    ->assertForbidden();

  // Control if cupcake has not been created
  expect(Coupon::count())
      ->toEqual(0);
});

test('an admin user can create a coupon', function()
{
  $coupon = [
    'name'=> 'reduc20',
    'start_date'=> "2024-12-10",
    'expire_date'=> "2024-12-15",
    'value'=> 20
  ];

  $responseCoupon = [
    'id'=> 1,
    'name'=> 'reduc20',
    'start_date'=> "2024-12-10",
    'expire_date'=> "2024-12-15",
    'value'=> 20
  ];

  // dd($coupon);

  /**
   * @var user
   */
  $user = User::factory()->admin()->create();

  // Get deletion response
  $response = actingAs($user)
    ->postJson(route('coupon.create'), $coupon)
    ->assertStatus(200);
  $responseCoupon = $response['data'];

  // Control if cupcake has been created
  expect(Coupon::count())
      ->toEqual(1);

  // Controll if created cupcake is the same as submitted cupcake
  expect($responseCoupon)
    ->toEqual($responseCoupon);
});