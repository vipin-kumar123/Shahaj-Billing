<?php

namespace App\Services;

use App\Models\Categories;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SubcategoryService
{

    public function categoryList()
    {
        return Categories::where('is_active', 1)->get();
    }

    public function subcategoryList()
    {
        return SubCategory::with('category')->latest();
    }

    public function subStore(array $data)
    {
        if (!empty($data['name']) && is_array($data['name'])) {

            foreach ($data['name'] as $index => $name) {

                SubCategory::create([
                    'user_id'      => Auth::id(),
                    'category_id'  => $data['category_id'],
                    'name'         => $name,
                    'slug'         => Str::slug($data['slug'][$index] ?? $name),
                    'description'  => $data['description'][$index] ?? null,
                    'ip'           => request()->ip()
                ]);
            }
        }

        return true;
    }


    public function subcatEdit(array $data)
    {
        return SubCategory::findOrFail($data['id']);
    }

    public function subcatUpdate(array $data)
    {
        $subcat = SubCategory::findOrFail($data['subcatid']);

        if (!$subcat) {
            return false;
        }

        $subcat->update([
            'user_id'      => Auth::id(),
            'category_id'  => $data['category_id'],
            'name'         => $data['name'],
            'slug'         => Str::slug($data['slug']),
            'description'  => $data['description'],
            'ip'           => request()->ip(),
        ]);

        return true;
    }


    public function subStatusUpdate(array $data)
    {
        $subcat = SubCategory::findOrFail($data['id']);

        if (!$subcat) {
            return false;
        }

        $subcat->is_active = $data['status'];
        $subcat->save();
        return true;
    }

    public function destroy(array $data)
    {
        $subcat = SubCategory::findOrFail($data['id']);
        return $subcat->delete();
    }

    public function display(array $data)
    {
        $sub = SubCategory::with('category')->findOrFail($data['id']);

        if (!$sub) {
            return false;
        }

        return $sub;
    }
}
