<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        $creator = Auth::user()->name ?? 'Excel Import';
        $categoryCache = [];

        // Ensure the public/uploads/products folder exists
        $uploadDir = public_path('uploads/products');
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0777, true, true);
        }

        foreach ($rows as $row) {
            // Find keys safely even if Excel headers have leading/trailing whitespace
            $cleanRow = [];
            foreach ($row as $key => $val) {
                $cleanRow[trim(strtolower($key))] = $val;
            }

            $name = trim($cleanRow['name'] ?? '');
            $price = $cleanRow['price'] ?? null;

            if (empty($name) || $price === null || $price === '') {
                continue;
            }

            // 1. Resolve or dynamically create Category
            $categoryName = trim($cleanRow['category'] ?? 'General');
            if (!isset($categoryCache[$categoryName])) {
                $category = Category::firstOrCreate(
                    ['name' => $categoryName],
                    [
                        'description' => 'Created via bulk product import',
                        'status'      => 1,
                        'created_by'  => $creator,
                    ]
                );
                $categoryCache[$categoryName] = $category->id;
            }
            $categoryId = $categoryCache[$categoryName];

            // 2. Download and save to public/uploads/products/
            $imagePath = null;
            $rawImage = trim($cleanRow['image'] ?? '');

            if (!empty($rawImage)) {
                if (filter_var($rawImage, FILTER_VALIDATE_URL)) {
                    try {
                        // withoutVerifying bypasses Windows cURL SSL issues
                        $response = Http::withoutVerifying()
                            ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                            ->timeout(6)
                            ->get($rawImage);

                        if ($response->successful()) {
                            $filename = time() . '_' . Str::random(12) . '.jpg';
                            file_put_contents($uploadDir . '/' . $filename, $response->body());
                            $imagePath = 'uploads/products/' . $filename;
                        }
                    } catch (\Throwable $e) {
                        // If direct download fails, save the URL directly as fallback
                        $imagePath = $rawImage;
                    }
                } else {
                    $imagePath = 'uploads/products/' . ltrim($rawImage, '/');
                }
            }

            // 3. Upsert Product
            Product::updateOrCreate(
                [
                    'name'        => $name,
                    'category_id' => $categoryId,
                ],
                [
                    'qty'         => intval($cleanRow['qty'] ?? 0),
                    'price'       => floatval($price),
                    'description' => $cleanRow['description'] ?? null,
                    'image'       => $imagePath,
                    'status'      => 1,
                    'created_by'  => $creator,
                ]
            );
        }
    }
}