<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // Show all products
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::where('status', 1)->get();

        return view('products.index', compact('products', 'categories'));
    }

    // Show create product form
    public function create()
    {
        $categories = Category::where('status', 1)->get();

        return view('products.create', compact('categories'));
    }

    // Store new product
    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'sku'             => 'nullable|string|max:100|unique:products,sku',
            'barcode'         => 'nullable|string|max:100',
            'category_id'     => 'required|exists:categories,id',
            'qty'             => 'required|integer|min:0',
            'price'           => 'required|numeric|min:0',
            'cost_price'      => 'nullable|numeric|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status'          => 'nullable|boolean',
            'featured'        => 'nullable|boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $imagePath = 'uploads/products/' . $filename;
        }

        $product = DB::transaction(function () use ($request, $imagePath) {
            $sku = $request->sku ? strtoupper(trim($request->sku)) : 'SKU-' . strtoupper(substr(uniqid(), -6));

            $product = Product::create([
                'name'            => $request->name,
                'sku'             => $sku,
                'barcode'         => $request->barcode,
                'category_id'     => $request->category_id,
                'qty'             => $request->qty,
                'price'           => $request->price,
                'cost_price'      => $request->cost_price ?? 0.00,
                'min_stock_level' => $request->min_stock_level ?? 5,
                'description'     => $request->description ?? '',
                'image'           => $imagePath ?? 'uploads/products/default.png',
                'status'          => $request->has('status') ? (bool) $request->status : true,
                'featured'        => $request->boolean('featured'),
                'created_by'      => Auth::user()->name ?? 'System',
            ]);

            // Record initial stock movement if qty > 0
            if ($product->qty > 0) {
                StockMovement::create([
                    'product_id'     => $product->id,
                    'type'           => 'initial',
                    'quantity'       => $product->qty,
                    'previous_stock' => 0,
                    'new_stock'      => $product->qty,
                    'reference_type' => 'Initial Stock',
                    'reference_id'   => $product->id,
                    'user_id'        => Auth::id(),
                    'user_name'      => Auth::user()->name ?? 'System',
                    'reason'         => 'Initial product creation stock',
                ]);
            }

            ActivityLog::log('Product Created', 'Product', $product->id, "Product '{$product->name}' created with {$product->qty} units.");

            return $product;
        });

        return redirect()
            ->route('product.index')
            ->with('success', 'Product created successfully.');
    }

    // Show edit product form
    public function edit(Product $product)
    {
        $categories = Category::where('status', 1)->get();

        return view('products.edit', compact('product', 'categories'));
    }

    // Update product
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'sku'             => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'barcode'         => 'nullable|string|max:100',
            'category_id'     => 'required|exists:categories,id',
            'qty'             => 'required|integer|min:0',
            'price'           => 'required|numeric|min:0',
            'cost_price'      => 'nullable|numeric|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'description'     => 'nullable|string',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status'          => 'required|boolean',
            'featured'        => 'nullable|boolean',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($product->image && File::exists(public_path($product->image)) && !str_contains($product->image, 'default.png')) {
                File::delete(public_path($product->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $imagePath = 'uploads/products/' . $filename;
        }

        DB::transaction(function () use ($request, $product, $imagePath) {
            $prevStock = $product->qty;
            $newStock = intval($request->qty);

            if ($prevStock !== $newStock) {
                $diff = $newStock - $prevStock;
                StockMovement::create([
                    'product_id'     => $product->id,
                    'type'           => 'adjustment',
                    'quantity'       => $diff,
                    'previous_stock' => $prevStock,
                    'new_stock'      => $newStock,
                    'reference_type' => 'Manual Edit',
                    'reference_id'   => null,
                    'user_id'        => Auth::id(),
                    'user_name'      => Auth::user()->name,
                    'reason'         => 'Manual stock update via product edit',
                ]);
            }

            $product->update([
                'name'            => $request->name,
                'sku'             => $request->sku ? strtoupper(trim($request->sku)) : $product->sku,
                'barcode'         => $request->barcode,
                'category_id'     => $request->category_id,
                'qty'             => $newStock,
                'price'           => $request->price,
                'cost_price'      => $request->cost_price ?? $product->cost_price,
                'min_stock_level' => $request->min_stock_level ?? 5,
                'description'     => $request->description ?? '',
                'image'           => $imagePath,
                'status'          => $request->status,
                'featured'        => $request->boolean('featured'),
                'updated_by'      => Auth::user()->name ?? 'System',
            ]);

            ActivityLog::log('Product Updated', 'Product', $product->id, "Product '{$product->name}' updated.");
        });

        return redirect()
            ->route('product.index')
            ->with('success', 'Product updated successfully.');
    }

    // Show upload form
    public function showImportForm()
    {
        return view('products.import');
    }

    // Process uploaded .xlsx or .xls file
    public function import(Request $request)
    {
        ini_set('max_execution_time', 600);
        set_time_limit(600);
        ini_set('memory_limit', '512M');

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            Excel::import(new ProductsImport, $request->file('excel_file'));

            ActivityLog::log('Products Imported', 'Product', null, "Bulk products imported from Excel file.");

            return redirect()
                ->route('product.index')
                ->with('success', 'Products and categories imported successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Import error: ' . $e->getMessage());
        }
    }

    // Delete product
    public function destroy(Product $product)
    {
        if ($product->image && File::exists(public_path($product->image)) && !str_contains($product->image, 'default.png')) {
            File::delete(public_path($product->image));
        }

        ActivityLog::log('Product Deleted', 'Product', $product->id, "Product '{$product->name}' deleted.");

        $product->delete();

        return redirect()
            ->route('product.index')
            ->with('success', 'Product deleted successfully.');
    }
}
