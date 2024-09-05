<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseResource;
use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchases = Purchase::with(['user', 'cupcakes'])->get();

        return PurchaseResource::collection($purchases);
    }

    // /**
    //  * Store a newly created resource in storage.
    //  */
    // public function store(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'name'=> 'required|string|min:2',
    //         'image'=> 'string',
    //         'quantity'=> 'numeric',
    //         'is_available'=> 'boolean',
    //         'is_advertised'=> 'boolean',
    //         'price'=> 'required|numeric|gt:0'
    //     ]);

    //     $cupcake = Cupcake::create($validatedData);
    //     // $cupcake->save();

    //     return response(['message'=>'cupcake successfully added', 'data'=>$cupcake], 200);
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(string $id)
    // {
    //     return new CupcakeResource(Cupcake::findOrFail($id));
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, string $id)
    // {
    //     $validatedData = $request->validate([
    //         'name'=> 'required|string|min:2',
    //         'image'=> 'string',
    //         'quantity'=> 'numeric',
    //         'is_available'=> 'boolean',
    //         'is_advertised'=> 'boolean',
    //         'price'=> 'required|numeric|gt:0'
    //     ]);

    //     /**
    //      * @var Cupcake $currentCupcake
    //      */
    //     $currentCupcake = Cupcake::findOrFail($id);

    //     $currentCupcake->fill($validatedData);

    //     $currentCupcake->save();

    //     return response(['message'=> 'cupcake successfully updated', 'data' => $currentCupcake]);
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $purchase = Purchase::findOrFail($id)->delete();

        if($purchase == 1) {
            return response(['message'=>'purchase with id '. $id . ' has been successfully destroyed']);
        }

        return $purchase;
    }
}
