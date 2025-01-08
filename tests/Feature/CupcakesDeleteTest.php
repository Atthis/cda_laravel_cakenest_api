<?php

use App\Models\Cupcake;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;

test('an admin user can delete a cupcake', function () {
    $adminUser = User::factory()->admin()->create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
    ]);

    $cupcake = Cupcake::factory()->create();

    $deletionMessage = [
        "message" => "cupcake with id 1 has been successfully destroyed"
    ];

    // Get deletion response
    $response = actingAs($adminUser)
        ->delete(route('cupcake.delete', [$cupcake->id]));

    // Control response message value
    expect($response->json())
        ->toEqual($deletionMessage);

    // Control if cupcake has been deleted
    expect(Cupcake::find($cupcake->id))
        ->toBeNull();
});

test('a standard user cannot delete a cupcake', function () {
    /**
     * @var user
     */
    $user = User::factory()->create([
        'name' => 'User',
        'email' => 'user@example.com',
    ]);


    $cupcake = Cupcake::factory()->create();

    // Get deletion response
    actingAs($user)
        ->deleteJson(route('cupcake.delete', [$cupcake->id]), [])
        ->assertForbidden();

    // Control if cupcake has not been deleted
    $dbCupcake = Cupcake::find($cupcake->id);
    expect($dbCupcake)
        ->not->toBeNull();
});

test('an anonymous user cannot delete a cupcake', function () {
    $cupcake = Cupcake::factory()->create();

    // Get deletion response
    deleteJson(route('cupcake.delete', [$cupcake->id]), [])
        ->assertUnauthorized();

    // Control if cupcake has not been deleted
    $dbCupcake = Cupcake::find($cupcake->id);
    expect($dbCupcake)
        ->not->toBeNull();
});
