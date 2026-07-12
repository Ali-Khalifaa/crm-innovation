<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingFeatureRequest;
use App\Http\Resources\Dashboard\LandingFeatureResource;
use App\Models\LandingFeature;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingFeatureController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing feature read',   only: ['index', 'show']),
            new Middleware('can:landing feature create', only: ['store']),
            new Middleware('can:landing feature edit',   only: ['update']),
            new Middleware('can:landing feature delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $features = LandingFeature::orderBy('sort_order')->orderBy('id')->paginate(15);
        return responseJson(LandingFeatureResource::collection($features->items()), '', 200, getPaginates($features));
    }

    public function store(LandingFeatureRequest $request)
    {
        LandingFeature::create($request->validated());
        return responseJson([], 'Created Successfully', 200);
    }

    public function show(LandingFeature $landingFeature)
    {
        return responseJson(new LandingFeatureResource($landingFeature), 'Data retrieved successfully', 200);
    }

    public function update(LandingFeatureRequest $request, LandingFeature $landingFeature)
    {
        $landingFeature->update($request->validated());
        return responseJson(new LandingFeatureResource($landingFeature), 'Updated Successfully', 200);
    }

    public function destroy(LandingFeature $landingFeature)
    {
        $landingFeature->delete();
        return responseJson([], 'Deleted Successfully', 200);
    }
}
