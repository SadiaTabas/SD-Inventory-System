<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    // Show all products
    public function index()
    {
        $products = Product::with('category')->latest()->get();

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
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'qty' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $imagePath = 'uploads/products/' . $filename;
        }

        Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'qty' => $request->qty,
            'price' => $request->price,
            'description' => $request->description ?? '',
            'image' => $imagePath ?? 'uploads/products/default.png',
            'created_by' => Auth::user()->name ?? 'System',
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
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'qty' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            // Delete old file if exists and is not default
            if ($product->image && File::exists(public_path($product->image)) && !str_contains($product->image, 'default.png')) {
                File::delete(public_path($product->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $imagePath = 'uploads/products/' . $filename;
        }

        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'qty' => $request->qty,
            'price' => $request->price,
            'description' => $request->description ?? '',
            'image' => $imagePath,
            'updated_by' => Auth::user()->name ?? 'System',
        ]);

        return redirect()
            ->route('product.index')
            ->with('success', 'Product updated successfully.');
    }

    // Delete product
    public function destroy(Product $product)
    {
        // Delete product image if exists
        if ($product->image && File::exists(public_path($product->image)) && !str_contains($product->image, 'default.png')) {
            File::delete(public_path($product->image));
        }

        $product->delete();

        return redirect()
            ->route('product.index')
            ->with('success', 'Product deleted successfully.');
    }
}