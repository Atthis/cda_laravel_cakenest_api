<?php

namespace App\Http\Controllers;

use App\Http\Resources\CupcakeResource;
use App\Http\Resources\PurchaseResource;
use App\Models\Cupcake;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if($user->is_admin == 1) {
            $purchases = Purchase::with(['user', 'cupcakes'])->get();
        } else {
            $purchases = Purchase::with(['user', 'cupcakes'])->where('user_id', '=', $user->id)->get();
        }

        return PurchaseResource::collection($purchases);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate front data
        $validatedData = $request->validate([
            'user_id'=> 'numeric|exists:users,id',
            'cupcakes'=> 'required|array',
            'cupcakes.*.cupcake_id'=> 'required|exists:cupcakes,id',
            'cupcakes.*.quantity'=> 'required|numeric|min:1'
        ]);

        // Check for cupcake stocks
        $out_of_stock_cupcakes = [];

        foreach ($validatedData['cupcakes'] as $cupcake) {
            $cupcake_data = Cupcake::findOrFail($cupcake['cupcake_id']);

            if ($cupcake['quantity'] > $cupcake_data->quantity) {
                array_push($out_of_stock_cupcakes, [
                    "cupcake_id" => $cupcake_data->id,
                    "cupcake_name" => $cupcake_data->name,
                    "stock_quantity" => $cupcake_data->quantity,
                    "requested_quantity" => $cupcake['quantity']
                ]);
                continue;
            }
        }

        // if some cupcakes have higher requested quantity than stock
        if (!empty($out_of_stock_cupcakes)) {
            return response(['message' => 'out of stock cupcakes inside the purchase.', 'outOfStockCupcakes' => $out_of_stock_cupcakes]);
        }

        // create new purchase with validated data
        $purchase = new Purchase([
            'user_id' => isset($validatedData['user_id']) ? $validatedData['user_id'] : $request->user()->id
        ]);

        // save purchase into db
        $purchase->save();

        // for each cupcake, associate it with the purchase
        foreach ($validatedData['cupcakes'] as $cupcake) {
            $cupcake_data = Cupcake::findOrFail($cupcake['cupcake_id']);

            // Add pivot data to the pivot table
            $purchase->cupcakes()->attach($cupcake_data->id, ['quantity' => $cupcake['quantity'], 'price' => $cupcake_data->price]);

            // Remove purchased quantity from stock
            $cupcake_data->fill(['quantity' => $cupcake_data->quantity - $cupcake['quantity']]);
            $cupcake_data->save();
        }

        return response(['message'=>'purchase successfully added', 'data'=>
        new PurchaseResource(Purchase::with(['user', 'cupcakes'])->findOrFail($purchase ->id))], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = $request->user();

        if($user->is_admin == 1) {
            return new PurchaseResource(Purchase::with(['user', 'cupcakes'])->findOrFail($id));
        }

        $purchase =  new PurchaseResource(Purchase::with(['user', 'cupcakes'])->findOrFail($id));

        if ($purchase->user->id == $user->id) {
            return new PurchaseResource(Purchase::with(['user', 'cupcakes'])->findOrFail($id));
        } else {
            return response(['message' => 'unauthorized'], Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // validate front data
        $validatedData = $request->validate([
            'user_id'=> 'numeric|exists:users,id',
            'cupcakes'=> 'required|array',
            'cupcakes.*.cupcake_id'=> 'required|exists:cupcakes,id',
            'cupcakes.*.quantity'=> 'required|numeric|min:1'
        ]);

        // Check for cupcake stocks
        $out_of_stock_cupcakes = [];

        foreach ($validatedData['cupcakes'] as $cupcake) {
            $cupcake_data = Cupcake::findOrFail($cupcake['cupcake_id']);

            if ($cupcake['quantity'] > $cupcake_data->quantity) {
                array_push($out_of_stock_cupcakes, [
                    "cupcake_id" => $cupcake_data->id,
                    "cupcake_name" => $cupcake_data->name,
                    "stock_quantity" => $cupcake_data->quantity,
                    "requested_quantity" => $cupcake['quantity']
                ]);
                continue;
            }
        }

        // if some cupcakes have higher requested quantity than stock
        if (!empty($out_of_stock_cupcakes)) {
            return response(['message' => 'out of stock cupcakes inside the purchase.', 'outOfStockCupcakes' => $out_of_stock_cupcakes]);
        }

        // get purchase from db
        $purchase = new PurchaseResource(Purchase::with(['user', 'cupcakes'])->findOrFail($id));

        $purchase_cupcakes = $purchase->cupcakes()->withPivot(['quantity', 'price'])->get();

        // Restore cupcakes stock
        foreach ($purchase_cupcakes as $cupcake) {
            $cupcake->fill(['quantity' => $cupcake->quantity + $cupcake->pivot->quantity]);
            $cupcake->save();
        }

        // for each cupcake, associate it with the purchase
        foreach ($validatedData['cupcakes'] as $cupcake) {
            $cupcake_data = Cupcake::findOrFail($cupcake['cupcake_id']);

            // Add pivot data to the pivot table
            $purchase->cupcakes()->syncWithPivotValues($cupcake_data->id, ['quantity' => $cupcake['quantity'], 'price' => $cupcake_data->price]);

            // Remove purchased quantity from stock
            $cupcake_data->fill(['quantity' => $cupcake_data->quantity - $cupcake['quantity']]);
            $cupcake_data->save();
        }

        return response(['message'=>'purchase successfully updated', 'data'=>
        new PurchaseResource(Purchase::with(['user', 'cupcakes'])->findOrFail($purchase ->id))], 200);
    }

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
