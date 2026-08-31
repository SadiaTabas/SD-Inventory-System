<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    // Show all products
    public function index()
    {
        $products = Product::with('category')->get();

        return view('products.index', [
            'products' => $products
        ]);
    }


    // Show create product form
    public function create()
    {
        // Get only active categories
        $categories = Category::where('status', 1)->get();

        return view('products.create', [
            'categories' => $categories
        ]);
    }


    // Store new product
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'qty' => 'required|integer',
            'price' => 'required|numeric',
            'description' => 'nullable',
        ]);

        Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'qty' => $request->qty,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('product.index')
            ->with('success', 'Product created successfully.');
    }


    // Show edit product form
    public function edit(Product $product)
    {
        // Get active categories
        $categories = Category::where('status', 1)->get();

        return view('products.edit', [
            'product' => $product,
            'categories' => $categories
        ]);
    }


    // Update product
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'qty' => 'required|integer',
            'price' => 'required|numeric',
            'description' => 'nullable',
        ]);

        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'qty' => $request->qty,
            'price' => $request->price,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('product.index')
            ->with('success', 'Product updated successfully.');
    }


    // Delete product
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('product.index')
            ->with('success', 'Product deleted successfully.');
    }
}
