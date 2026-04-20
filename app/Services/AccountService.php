<?php

namespace App\Services;

use App\Models\Account;
use FFI\Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountService
{


    public function params($userId)
    {
        return [
            'accounts' => Account::where('user_id', $userId)
                ->orderBy('type')
                ->orderBy('name')
                ->get()
        ];
    }

    public function accountList($userId)
    {
        return Account::with('parent')->latest()->where('user_id', $userId);
    }


    public function saveAccount(array $data, int $userId): bool
    {
        // Check parent account type match
        if (!empty($data['parent_id'])) {
            $parent = Account::where('user_id', $userId)
                ->find($data['parent_id']);

            if (!$parent || $parent->type !== $data['type']) {
                return false;
            }
        }

        Account::create([
            'user_id'   => $userId,
            'name'      => $data['name'],
            'type'      => $data['type'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        return true;
    }


    public function editAccount(array $data)
    {
        return Account::findOrFail($data['account_id']);
    }

    public function accountUpdate(array $data, $id)
    {
        $account = Account::findOrFail($id);

        $account->update([
            'name'      => $data['name'],
            'type'      => $data['type'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        return $account;
    }


    public function deleteAccount(array $data)
    {
        return DB::transaction(function () use ($data) {

            $account = Account::withCount('children')->findOrFail($data['account_id']);

            // Check if account has child accounts
            if ($account->children_count > 0) {
                throw new \Exception('Cannot delete account because it has child accounts.');
            }

            $account->delete();

            return true;
        });
    }
}
