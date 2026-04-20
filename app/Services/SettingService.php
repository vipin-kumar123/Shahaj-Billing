<?php

namespace App\Services;

use App\Models\Cities;
use App\Models\CompanyDetails;
use App\Models\SiteSetting;
use App\Models\State;
use Illuminate\Validation\Rules\Exists;

class SettingService
{
    public function master_parms()
    {
        return [
            'stateData' => State::where('is_active', 1)->get(),
        ];
    }

    public function GetSettingGeneralData()
    {
        return SiteSetting::where('user_id', auth()->id())->first();
    }

    public function GeneratSettings(array $data)
    {
        return SiteSetting::updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );
    }

    public function settingLogo(array $data)
    {
        $setting = SiteSetting::where('user_id', auth()->id())->first();

        if (isset($data['logo']) && $data['logo']->isValid()) {

            $folder = 'assets/backend/uploads/settings/logo/';

            if ($setting->logo && file_exists(public_path($setting->logo))) {
                @unlink(public_path($setting->logo));
            }

            $filename = time() . '_' . uniqid() . '.' . $data['logo']->getClientOriginalExtension();
            $data['logo']->move(public_path($folder), $filename);

            $data['logo'] = $folder . $filename;
        } else {
            unset($data['logo']);
        }

        if (isset($data['favicon']) && $data['favicon']->isValid()) {

            $folder = 'assets/backend/uploads/settings/favicon/';

            if ($setting->favicon && file_exists(public_path($setting->favicon))) {
                @unlink(public_path($setting->favicon));
            }

            $filename = time() . '_' . uniqid() . '.' . $data['favicon']->getClientOriginalExtension();
            $data['favicon']->move(public_path($folder), $filename);

            $data['favicon'] = $folder . $filename;
        } else {
            unset($data['favicon']);
        }

        return $setting->update($data);
    }



    /************************COMPANY*****************************************/
    public function stateWiseCity(array $data)
    {
        return Cities::where('state_id', $data['stateId'])->where('is_active', 1)->get();
    }


    public function companyData(array $data)
    {
        $userId = auth()->id();

        $company = CompanyDetails::where('user_id', $userId)->first();

        $fileUpload = $company->company_logo ?? null;

        if (!empty($data['company_logo']) && $data['company_logo']->isValid()) {

            if (!empty($company) && !empty($company->company_logo) && file_exists(public_path($company->company_logo))) {
                @unlink(public_path($company->company_logo));
            }

            $folder = "assets/backend/uploads/company/" . $userId . '/';
            if (!file_exists(public_path($folder))) {
                mkdir(public_path($folder), 0777, true);
            }

            $logo = $data['company_logo'];
            $filename = time() . uniqid() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path($folder), $filename);

            $fileUpload = $folder . $filename;
        }

        return CompanyDetails::updateOrCreate(
            ['user_id' => $userId],
            [
                'name'         => $data['name'] ?? null,
                'email'        => $data['email'] ?? null,
                'mobile'       => $data['mobile'] ?? null,
                'tax_number'   => $data['tax_number'] ?? null,
                'address'      => $data['address'] ?? null,
                'state_id'     => $data['state_id'] ?? null,
                'city_id'      => $data['city_id'] ?? null,
                'company_logo' => $fileUpload,
                'ip'           => request()->ip(),
            ]
        );
    }

    public function companyGetDetails()
    {
        return CompanyDetails::where('user_id', auth()->id())->first();
    }

    /************************COMPANY*****************************************/
}
