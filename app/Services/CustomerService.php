<?php

namespace App\Services;

use App\Models\Cities;
use App\Models\Customer;
use App\Models\State;
use Illuminate\Support\Facades\Auth;

class CustomerService
{

    public function commanData()
    {
        return [
            'state' => State::where('is_active', 1)->get(),
            'cities' => Cities::where('is_active', 1)->get()
        ];
    }


    public function CustomerList()
    {
        return Customer::with('user')->where('type', 'customer')->latest();
    }


    public function customerStore(array $data)
    {
        $data['user_id'] = Auth::id();
        $data['ip'] = request()->ip();
        return Customer::create($data);
    }


    public function editCustomer($id)
    {
        return Customer::find($id);
    }

    public function updateCustomer(array $data, $id)
    {
        $customer = Customer::findOrFail($id);
        return $customer->update($data);
    }

    public function statusUpdate(array $data)
    {
        $customer = Customer::find($data['id']);

        if (!$customer) {
            return false;
        }

        $customer->is_active = $data['status'];

        return $customer->save();
    }

    public function destroy(array $data)
    {
        $customer = Customer::find($data['id']);

        if (!$customer) {
            return false;
        }

        return $customer->delete();
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
}
