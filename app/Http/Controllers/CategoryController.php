<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    // Show all categories
    public function index()
    {
        $categories = Category::latest()->get();

        return view('categories.index', [
            'categories' => $categories
        ]);
    }

    // Show create category form
    public function create()
    {
        return view('categories.create');
    }

    // Store new category (handles dynamic multiple categories and single fallback)
    public function store(Request $request)
    {
        $creatorName = Auth::user()->name ?? 'System';

        // 1. Dynamic Array Submission
        if ($request->has('categories') && is_array($request->categories)) {
            $request->validate([
                'categories'               => 'required|array|min:1',
                'categories.*.name'        => 'required|string|max:255|distinct|unique:categories,name',
                'categories.*.description' => 'nullable|string',
                'categories.*.status'      => 'nullable|in:0,1',
            ], [
                'categories.*.name.distinct' => 'Each category name in the list must be unique.',
                'categories.*.name.unique'   => 'One or more category names already exist in the database.',
            ]);

            DB::transaction(function () use ($request, $creatorName) {
                foreach ($request->categories as $item) {
                    Category::create([
                        'name'        => $item['name'],
                        'description' => $item['description'] ?? null,
                        'status'      => isset($item['status']) ? (bool) $item['status'] : true,
                        'created_by'  => $creatorName,
                    ]);
                }
            });

            return redirect()
                ->route('category.index')
                ->with('success', 'Categories created successfully.');
        }

        // 2. Single Category Submission Fallback
        $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'status'      => 'nullable|boolean',
        ]);

        Category::create([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->has('status') ? (bool) $request->status : true,
            'created_by'  => $creatorName,
        ]);

        return redirect()
            ->route('category.index')
            ->with('success', 'Category created successfully.');
    }

    // Show edit form
    public function edit(Category $category)
    {
        return view('categories.edit', [
            'category' => $category
        ]);
    }

    // Update category
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ]);

        $category->update([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status,
            'updated_by'  => Auth::user()->name ?? 'System',
        ]);

        return redirect()
            ->route('category.index')
            ->with('success', 'Category updated successfully.');
    }

    // Delete category
    public function destroy(Category $category)
    {
        // Prevent deletion if category contains products
        if ($category->products()->count() > 0) {
            return redirect()
                ->route('category.index')
                ->with('error', 'Cannot delete category containing existing products.');
        }

        $category->delete();

        return redirect()
            ->route('category.index')
            ->with('success', 'Category deleted successfully.');
    }
}