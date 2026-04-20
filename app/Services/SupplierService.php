<?php

namespace App\Services;

use App\Models\Cities;
use App\Models\Customer;
use App\Models\State;
use Illuminate\Support\Facades\Auth;

class SupplierService
{
    public function commanData()
    {
        return [
            'state' => State::where('is_active', 1)->get(),
            'cities' => Cities::where('is_active', 1)->get()
        ];
    }


    public function supplierList()
    {
        return Customer::where('type', 'supplier')->latest();
    }


    public function supplierStore(array $data)
    {
        $data['type'] = 'supplier';
        $data['user_id'] = Auth::id();
        $data['ip'] = request()->ip();

        return Customer::create($data);
    }


    public function statusUpdate(array $data)
    {
        $supplier = Customer::find($data['id']);

        if (!$supplier) {
            return false;
        }

        $supplier->is_active = $data['status'];
        return $supplier->save();
    }


    public function editSupplier($id)
    {
        return Customer::findOrFail($id);
    }

    public function getCity(array $data)
    {
        $state = Cities::where('is_active', 1)
            ->where('state_id', $data['state_id'])
            ->get();

        if ($state->isEmpty()) {
            return false;
        }

        return $state;
    }


    public function supplierUpdate(array $data, $id)
    {
        $supplier = Customer::find($id);
        return $supplier->update($data);
    }
}
