<?php

use App\Http\Controllers\CupcakeController;
use App\Http\Resources\CupcakeResource;
use App\Models\Cupcake;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

test('an unauthendicated user cannot create a cupcake', function()
{
  $cupcake = [
    'name'=> 'Orange',
    'quantity'=> '50',
    'flavor'=> 'mix',
    'is_available'=> true,
    'is_advertised'=> false,
    'price'=> 2.90
  ];

  postJson(route('cupcake.create'), $cupcake)
      ->assertUnauthorized();

  // Control if cupcake has not been created
  expect(Cupcake::count())
      ->toEqual(0);
});

test('a non-admin user cannot create a cupcake', function()
{
  $cupcake = [
    'name'=> 'Orange',
    'quantity'=> '50',
    'flavor'=> 'mix',
    'is_available'=> true,
    'is_advertised'=> false,
    'price'=> 2.90
  ];

  /**
   * @var user
   */
  $user = User::factory()->create();

  actingAs($user)
    ->postJson(route('cupcake.create'), $cupcake)
    ->assertForbidden();

  // Control if cupcake has not been created
  expect(Cupcake::count())
      ->toEqual(0);
});

test('an admin user can create a cupcake', function()
{
  $cupcake = [
    'name'=> 'Orange',
    'quantity'=> 50,
    'flavor'=> 'mix',
    'is_available'=> true,
    'is_advertised'=> false,
    'price'=> 2.9
  ];

  $cupcakeResponse = [
    'id'=> 1,
    'title'=> 'Orange',
    'quantity'=> 50,
    'flavor'=> 'mix',
    'is_available'=> true,
    'is_advertised'=> false,
    'price'=> 2.9,
    'imageSource'=> null
  ];

  /**
   * @var user
   */
  $user = User::factory()->admin()->create();

  // Get deletion response
  $response = actingAs($user)
    ->postJson(route('cupcake.create'), $cupcake)
    ->assertStatus(200);
  $responseCupcake = $response['data'];

  // Control if cupcake has been created
  expect(Cupcake::count())
      ->toEqual(1);

  // Controll if created cupcake is the same as submitted cupcake
  expect($responseCupcake)
    ->toEqual($cupcakeResponse);
});

test("invalid form data shouldn't be submitted", function()
{
  $cupcakeWithEmptyName = [
    'name'=> null,
    'quantity'=> 10,
    'flavor'=> 'mix',
    'is_available'=> true,
    'is_advertised'=> false,
    'price'=> 2.9
  ];
  $cupcakeWithWrongQuantity = [
    'name'=> 'Orange',
    'quantity'=> 'test',
    'flavor'=> 'mix',
    'is_available'=> true,
    'is_advertised'=> false,
    'price'=> 2.9
  ];
  $cupcakeWithWrongFlavor = [
    'name'=> 'Orange',
    'quantity'=> 'test',
    'flavor'=> 'gross',
    'is_available'=> true,
    'is_advertised'=> false,
    'price'=> 2.9
  ];

  $cupcakes = [
    $cupcakeWithEmptyName,
    $cupcakeWithWrongQuantity,
    $cupcakeWithWrongFlavor
  ];

  /**
   * @var user
   */
  $user = User::factory()->admin()->create();

  foreach ($cupcakes as $cupcake) {
    actingAs($user)
    ->postJson(route('cupcake.create'), $cupcake)
    ->assertUnprocessable();
  }
});