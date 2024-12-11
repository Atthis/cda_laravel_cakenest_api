<?php

use App\Models\Cupcake;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\patchJson;

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

  $dbCupcake = Cupcake::create($cupcake);

  $newCupcakeData = [
    'name'=> 'Blue',
  ];

  patchJson(route('cupcake.update', $dbCupcake->id), $newCupcakeData)
      ->assertUnauthorized();

  // Control if the cupcake has not been updated
  expect(Cupcake::find(1)->name)
      ->not->toEqual($newCupcakeData['name']);
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

  $dbCupcake = Cupcake::create($cupcake);

  $newCupcakeData = [
    'name'=> 'Blue',
  ];

  /**
   * @var user
   */
  $user = User::factory()->create();

  actingAs($user)
    ->patchJson(route('cupcake.update', $dbCupcake->id), $newCupcakeData)
      ->assertForbidden();

  // Control if the cupcake has not been updated
  expect(Cupcake::find(1)->name)
      ->not->toEqual($newCupcakeData['name']);
});

test('an admin user can create a cupcake', function()
{
  $cupcake = [
    'name'=> 'Orange',
    'quantity'=> '50',
    'flavor'=> 'mix',
    'is_available'=> true,
    'is_advertised'=> false,
    'price'=> 2.90
  ];

  $dbCupcake = Cupcake::create($cupcake);

  $newCupcakeData = [
    'name'=> 'Blue',
    'price'=> 2.90
  ];

  /**
   * @var user
   */
  $user = User::factory()->admin()->create();

  // Get deletion response
  $response = actingAs($user)
    ->patchJson(route('cupcake.update', $dbCupcake->id), $newCupcakeData)
    ->assertStatus(200);
  $responseCupcake = $response['data'];

  // Controll if updated cupcake data is as expected
  expect($responseCupcake['title'])
    ->toEqual($newCupcakeData['name']);
});

test('invalid form data shouldn\'t be submitted', function()
{
  $cupcake = [
    'name'=> 'Orange',
    'quantity'=> '50',
    'flavor'=> 'mix',
    'is_available'=> true,
    'is_advertised'=> false,
    'price'=> 2.90
  ];

  $dbCupcake = Cupcake::create($cupcake);

  $cupcakeWithEmptyName = [
    'name'=> null,
    'price'=> 2.9
  ];
  $cupcakeWithNoPrice = [
    'name'=> 'Blue',
    'price'=> null
  ];

  $cupcakes = [
    $cupcakeWithEmptyName,
    $cupcakeWithNoPrice
  ];

  /**
   * @var user
   */
  $user = User::factory()->admin()->create();

  foreach ($cupcakes as $cupcake) {
    actingAs($user)
    ->patchJson(route('cupcake.update', $dbCupcake->id), $cupcake)
    ->assertUnprocessable();

    // Control if the cupcake has not been updated
    if ($cupcake['name'] == null) {
      expect(Cupcake::find(1)->name)
      ->not->toEqual($cupcake['name']);
    }
    if ($cupcake['price'] == null) {
      expect(Cupcake::find(1)->price)
      ->not->toEqual($cupcake['price']);
    }
  }
});