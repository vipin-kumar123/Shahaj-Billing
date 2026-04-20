<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Categories;
use App\Models\Product;
use App\Models\SubCategory;

class ProductService
{
    public function master_data()
    {
        return [
            'categories' => Categories::where('is_active', 1)->get(),
            'subcategories' => SubCategory::where('is_active', 1)->get(),
            'brands' => Brand::where('is_active', 1)->get(),
        ];
    }

    public function GetCategory(array $data)
    {
        return SubCategory::where('category_id', $data['category_id'])
            ->where('is_active', 1)
            ->get();
    }

    public function saved_product(array $data)
    {
        $data['user_id'] = auth()->id();
        $data['ip'] = request()->ip();

        Product::create($data);

        return true;
    }

    public function product_list()
    {
        return Product::with('brand')->latest();
    }


    public function edit_product($id)
    {
        return Product::with(['category', 'brand'])->findOrFail($id);
    }

    public function update_product(array $data, $id)
    {
        $product = Product::findOrFail($id);

        $data['user_id'] = auth()->id();
        $data['ip'] = request()->ip();

        return $product->update($data);
    }


    public function show_product(string $id)
    {
        return Product::with(['category', 'subcategory', 'brand'])->findOrFail($id);
    }

    public function destroy($id)
    {
        $item = Product::find($id);

        if (!$item) {
            return false;
        }

        return $item->delete();
    }
}
