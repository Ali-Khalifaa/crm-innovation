<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingSettingRequest;
use App\Http\Resources\Dashboard\LandingSettingResource;
use App\Models\LandingSetting;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingSettingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing settings read',  only: ['show']),
            new Middleware('can:landing settings edit',  only: ['update']),
        ];
    }

    public function show()
    {
        $setting = LandingSetting::firstOrCreate([]);
        return responseJson(new LandingSettingResource($setting), 'Data retrieved successfully', 200);
    }

    public function update(LandingSettingRequest $request)
    {
        $setting = LandingSetting::firstOrCreate([]);
        $setting->update($request->validated());
        return responseJson(new LandingSettingResource($setting), 'Updated Successfully', 200);
    }
}
