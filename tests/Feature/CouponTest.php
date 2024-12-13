<?php

// Coupon tests
// - i can create a coupon only if i'm an administator
// - i can associate a coupon to a purchase if i'm authenticated
// - when the coupon is added to the purchase, the purchase total is reduce depending on the coupon value -> see the purchase resource for total calculation
// - if the coupon is expired, it cannot apply

use App\Models\Coupon;
use App\Models\Cupcake;
use App\Models\Purchase;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertObjectHasProperty;

function createPurchaseWithCoupon($is_expired = false)
{
    // Create a purchase
  /**
   * @var user
   */
  $user = User::factory()->create();
  $cupcakes = Cupcake::factory()->count(3)->create();
  if ($is_expired) {
    $coupon = Coupon::factory()->expired()->create();
  } else {
    $coupon = Coupon::factory()->create();
  }
  $purchase = [
    'user_id'=> $user->id,
    'cupcakes'=> [
      [
        'cupcake_id'=> $cupcakes[0]->id,
        'quantity'=> rand(1,3)
      ],
      [
        'cupcake_id'=> $cupcakes[1]->id,
        'quantity'=> rand(1,3)
      ],
      [
        'cupcake_id'=> $cupcakes[2]->id,
        'quantity'=> rand(1,3)
      ],
    ],
    'coupon_id'=> $coupon->id
  ];

  return [
    'user'=> $user,
    'purchase'=> $purchase
  ];
}

// ******************
// Coupon Creation tests
// ******************
test('an unauthendicated user cannot create a coupon', function()
{
  $coupon = [
    'code'=> 'reduc20',
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
    'code'=> 'reduc20',
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
    'code'=> 'reduc20',
    'start_date'=> "2024-12-10",
    'expire_date'=> "2024-12-15",
    'value'=> 20
  ];

  $responseCoupon = [
    'id'=> 1,
    'code'=> 'reduc20',
    'start_date'=> "2024-12-10",
    'expire_date'=> "2024-12-15",
    'value'=> 20
  ];

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

// ******************
// Coupon Apply tests
// ******************
test('an authenticated user can send a purchase with a coupon', function()
{
  ['user' => $user, 'purchase'=> $purchase] = createPurchaseWithCoupon();


  ['data'=> $dbPurchase] = actingAs($user)
  ->postJson(route('purchase.create'), $purchase)
  ->assertCreated();

  // test if purchase has been created
  expect(Purchase::count())
    ->toEqual(1);

  // dd($dbPurchase);

  // test if purchase has a coupon associated to it
  expect($dbPurchase['coupon_id'])
    ->toEqual(1);
});

test('the purchase total is reduce by the coupon amount', function()
{
  ['user' => $user, 'purchase'=> $purchase] = createPurchaseWithCoupon();

  // user send the purchase
  ['data'=> $dbPurchase] = actingAs($user)
  ->postJson(route('purchase.create'), $purchase);

  $purchaseCupcakes = Purchase::with('cupcakes')->find($dbPurchase['id'])->cupcakes;
  $totalPurchaseWithoutCoupon = 0;

  foreach ($purchaseCupcakes as $cupcake) {
    $cupcake_total = floor($cupcake->pivot->price) * $cupcake->pivot->quantity;
    $totalPurchaseWithoutCoupon += $cupcake_total;
}

  $couponValue = Coupon::find(Purchase::find($dbPurchase['id'])->coupon_id)->value;
  $totalPurchaseWithCoupon = ($totalPurchaseWithoutCoupon - floor($totalPurchaseWithoutCoupon * $couponValue / 100)) / 100;

  expect($dbPurchase['purchase_total'])
   ->toEqual($totalPurchaseWithCoupon);
});

test('the coupon can\'t be applied if it\'s expired', function()
{
  ['user' => $user, 'purchase'=> $purchase] = createPurchaseWithCoupon(true);

  // user send the purchase
  ['data'=> $responseData] = actingAs($user)
  ->postJson(route('purchase.create'), $purchase);

  // the purchase must not be saved because the coupon is expired
  expect(Purchase::count())
    ->toEqual(0);

  // it must return a error message saying the coupon is expired
  expect($responseData['message'])
    ->toEqual('your coupon can\'t be applied because it\'s expired.');
});