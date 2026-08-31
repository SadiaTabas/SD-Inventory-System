<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\product;

class ProductApiController extends Controller
{
    public function index()
    {
        $products = product::all();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $product = product::create([
            'name' => $request->name,
            'qty' => $request->qty,
            'price' => $request->price,
            'description' => $request->description
        ]);

        return response()->json($product, 201);
    }

    public function show(product $product)
    {
        return response()->json($product);
    }

    public function update(product $product, Request $request)
    {
        $product->update([
            'name' => $request->name,
            'qty' => $request->qty,
            'price' => $request->price,
            'description' => $request->description
        ]);

        return response()->json($product);
    }

    public function destroy(product $product)
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}