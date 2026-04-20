<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Cities;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{

    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function appSetting()
    {
        $data = $this->settingService->master_parms();
        return view('backend.settings.app-settings', $data);
    }

    public function getGeneralData()
    {
        $data = $this->settingService->GetSettingGeneralData();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'No settings found'
            ]);
        }
        // CONVERT PATH TO FULL URL
        if ($data->logo) {
            $data->logo = $data->logo
                ? asset($data->logo)
                : asset('assets/backend/images/faces/default.png');
        }

        if ($data->favicon) {
            $data->favicon = $data->favicon
                ? asset($data->favicon)
                : asset('assets/backend/images/faces/default.png');
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function General(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_name'      => 'required',
            'footer_text'   => 'nullable',
            'language'      => 'nullable',
            'timezone'      => 'nullable',
            'date_format'   => 'nullable',
            'time_format'   => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $this->settingService->GeneratSettings($validator->validate());

        return response()->json([
            'success' => true,
            'message' => 'General settings updated!'
        ]);
    }


    public function logoUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo'    => 'nullable|image|mimes:png,jpg,jpeg,svg,webp',
            'favicon' => 'nullable|image|mimes:png,jpg,jpeg,ico,webp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $this->settingService->settingLogo($validator->validate());

        return response()->json([
            'success' => true,
            'message' => 'App site logo update'
        ]);
    }



    /****************************COMPANY SECTION************************************/

    public function getCities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stateId'    => 'required|exists:states,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cities = $this->settingService->stateWiseCity($validator->validate());

        return response()->json([
            'cities' => $cities
        ]);
    }


    public function companyStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'email'        => 'nullable|email|max:255',
            'mobile'       => 'required|digits:10',
            'tax_number'   => 'nullable|string|max:50',
            'address'      => 'nullable|string',
            'state_id'     => 'nullable|numeric',
            'city_id'      => 'nullable|numeric',
            'company_logo' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $this->settingService->companyData($validator->validate());

        return response()->json([
            'success' => true,
            'message' => 'Company details updated successfully!'
        ]);
    }

    public function CompanyGetData()
    {
        $data = $this->settingService->companyGetDetails();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /****************************COMPANY SECTION************************************/
}
