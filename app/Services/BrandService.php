<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Support\Facades\Auth;

class BrandService
{

    public function brandList($userId)
    {
        return Brand::latest()->where('user_id', $userId);
    }

    public function saveBrand(array $data)
    {
        $data['user_id'] = Auth::id();
        $data['ip'] = request()->ip();

        return Brand::create($data);
    }


    public function destroy(array $data)
    {
        $brand = Brand::find($data['id']);
        return $brand->delete();
    }

    public function editBrand(array $data)
    {
        return Brand::findOrFail($data['id']);
    }

    public function updateBrand(array $data)
    {
        $brand = Brand::findOrFail($data['brand_id']);

        $data['user_id'] = Auth::id();
        $data['ip'] = request()->ip();

        return $brand->update($data);
    }

    public function showBrand(array $data)
    {
        return Brand::with('user')->findOrFail($data['id']);
    }
}
