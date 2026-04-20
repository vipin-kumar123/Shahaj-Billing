<?php

namespace App\Services;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryService
{

    public function categoryList()
    {
        return Categories::latest();
    }

    public function storeCategory(array $data)
    {
        Categories::create([
            'user_id'     => Auth::id(),
            'name'        => $data['name'],
            'slug'        => $data['slug'],
            'description' => $data['description'],
        ]);

        return true;
    }

    public function isActive(array $data)
    {
        $category = Categories::find($data['id']);

        $category->update([
            'is_active'        => $data['status'],
        ]);

        return true;
    }

    public function categoryEdit($id)
    {
        return Categories::find($id);
    }


    public function categoryUpdate(array $data)
    {
        $category = Categories::find($data['catid']);

        $category->update([
            'user_id' => Auth::id(),
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'],
        ]);

        return true;
    }

    public function categoryDelete(array $data)
    {
        return Categories::where('id', $data['id'])->delete();
    }
}
