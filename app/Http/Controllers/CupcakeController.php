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
    public function index()
    {
        return CupcakeResource::collection(Cupcake::where('is_available', '=', true)->get());
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
            'is_available'=> 'boolean',
            'is_advertised'=> 'boolean',
            'price'=> 'required|numeric|gt:0'
        ]);

        $cupcake = Cupcake::create($validatedData);
        // $cupcake->save();

        return response(['message'=>'cupcake successfully added', 'data'=>$cupcake], 200);
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
