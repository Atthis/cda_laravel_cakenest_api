<?php

namespace App\Http\Controllers;

use App\Http\Resources\CupcakeResource;
use App\Models\Cupcake;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class CupcakeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validatedFilters = $request->validate([
            'flavor'=> 'string|in:sucré,salé,mix',
            'price'=> 'string|in:asc,desc'
        ]);

        $user = $request->user();
        $filters = [];

        if (isset($validatedFilters['flavor'])) {
            array_push($filters, ['flavor', '=', $validatedFilters['flavor']]);
        }

        if ($user->is_admin) {
            if (isset($validatedFilters['price'])) {
                return CupcakeResource::collection(Cupcake::where($filters)->orderBy('price_in_cents', $validatedFilters['price'])->get());
            }

            return CupcakeResource::collection(Cupcake::where($filters)->get());
        } else {
            array_push($filters, ['is_available', '=', true]);

            if (isset($validatedFilters['price'])) {
                return CupcakeResource::collection(Cupcake::where($filters)->orderBy('price_in_cents', $validatedFilters['price'])->get());
            }

            return CupcakeResource::collection(Cupcake::where($filters)->get());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'=> 'required|string|min:2',
            'image'=> 'string',
            'quantity'=> 'numeric',
            'flavor'=> 'required|string|in:sucré,salé,mix',
            'is_available'=> 'boolean',
            'is_advertised'=> 'boolean',
            'price'=> 'required|numeric|gt:0'
        ]);

        $cupcake = Cupcake::create($validatedData);

        return response(['message'=>'cupcake successfully added', 'data'=>$cupcake]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new CupcakeResource(Cupcake::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name'=> 'required|string|min:2',
            'image'=> 'string',
            'quantity'=> 'numeric',
            'flavor'=> 'string|in:sucré,salé,mix',
            'is_available'=> 'boolean',
            'is_advertised'=> 'boolean',
            'price'=> 'required|numeric|gt:0'
        ]);

        /**
         * @var Cupcake $currentCupcake
         */
        $currentCupcake = Cupcake::findOrFail($id);

        $currentCupcake->fill($validatedData);

        $currentCupcake->save();

        return response(['message'=> 'cupcake successfully updated', 'data' => $currentCupcake]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cupcake = Cupcake::findOrFail($id)->delete();

        if($cupcake == 1) {
            return response(['message'=>'cupcake with id '. $id . ' has been successfully destroyed']);
        }

        return $cupcake;
    }
}
