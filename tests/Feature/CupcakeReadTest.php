<?php

use App\Models\Cupcake;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

test('every user, except admin, can see only available cupcakes', function() {
  Cupcake::factory()->count(3)->is_available()->create();
  Cupcake::factory()->count(2)->is_unavailable()->create();

  $availableCupcakes = Cupcake::where("is_available", true)->get();

  // Get all available cupcakes
  $response = getJson(route('cupcake.all'), []);

  $data = $response["data"];

  // Number of results is equal to all available cupcakes
  expect(count($data))
    ->toEqual(count($availableCupcakes));

  // Each cupcake has "is_available" to true
  foreach ($data as $cupcake) {
    expect($cupcake["is_available"])
      ->toEqual(true);
  }
});

test('admin can see all cupcakes', function() {
  $adminUser = User::factory()->admin()->create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
  ]);

  Cupcake::factory()->count(3)->is_available()->create();
  Cupcake::factory()->count(2)->is_unavailable()->create();

  // Get all available cupcakes
  $response = actingAs($adminUser)->getJson(route('cupcake.all'), []);

  $data = $response["data"];

  // Number of results is equal to all available cupcakes
  expect(count($data))
    ->toEqual(5);
});

test('Filters works for each filter', function() {
  Cupcake::factory()->create([
    'name' => 'Blue',
    'quantity' => random_int(0, 500),
    'flavor' => 'salé',
    'is_available' => true,
    'is_advertised' => false,
    'price_in_cents' => 300
  ]);
  Cupcake::factory()->create([
    'name' => 'Red',
    'quantity' => random_int(0, 500),
    'flavor' => 'salé',
    'is_available' => true,
    'is_advertised' => false,
    'price_in_cents' => 450
  ]);
  Cupcake::factory()->create([
    'name' => 'Yellow',
    'quantity' => random_int(0, 500),
    'flavor' => 'sucré',
    'is_available' => true,
    'is_advertised' => false,
    'price_in_cents' => 250
  ]);

  // *******
  // flavor filter tests
  // *******
  $saltyCupcakes = Cupcake::where("flavor", 'salé')->get();

  // Get all available cupcakes
  $saltyCupcakesResponse = getJson(route('cupcake.all', ["flavor" => "salé"]), []);

  $saltyData = $saltyCupcakesResponse["data"];

  // Number of results is equal to all available cupcakes
  expect(count($saltyData))
    ->toEqual(count($saltyCupcakes));

  // Each cupcake has "is_available" to true
  foreach ($saltyData as $cupcake) {
    expect($cupcake["flavor"])
      ->toEqual("salé");
  }

  //
  // Get all available cupcakes sorted by price ascendent
  $cupcakesResponse = getJson(route('cupcake.all', ["price" => "asc"]), []);

  $cupcakesResponseData = $cupcakesResponse['data'];

  $sortedResponseData = $cupcakesResponseData;
  usort($cupcakesResponseData, function($a, $b) {
    return $a['price'] <=> $b['price'];
  });

  expect($cupcakesResponseData)
    ->toMatchArray($sortedResponseData);
});
