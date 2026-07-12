<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingHowItWorkRequest;
use App\Http\Resources\Dashboard\LandingHowItWorkResource;
use App\Models\LandingHowItWork;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingHowItWorkController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing how it works read',   only: ['index', 'show']),
            new Middleware('can:landing how it works create', only: ['store']),
            new Middleware('can:landing how it works edit',   only: ['update']),
            new Middleware('can:landing how it works delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $steps = LandingHowItWork::orderBy('sort_order')->orderBy('step_number')->paginate(15);
        return responseJson(LandingHowItWorkResource::collection($steps->items()), '', 200, getPaginates($steps));
    }

    public function store(LandingHowItWorkRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = store_single_image($request->image);
        }
        LandingHowItWork::create($data);
        return responseJson([], 'Created Successfully', 200);
    }

    public function show(LandingHowItWork $landingHowItWork)
    {
        return responseJson(new LandingHowItWorkResource($landingHowItWork), 'Data retrieved successfully', 200);
    }

    public function update(LandingHowItWorkRequest $request, LandingHowItWork $landingHowItWork)
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            if ($landingHowItWork->image) {
                unlink_image_by_path($landingHowItWork->image);
            }
            $data['image'] = store_single_image($request->image);
        }
        $landingHowItWork->update($data);
        return responseJson(new LandingHowItWorkResource($landingHowItWork), 'Updated Successfully', 200);
    }

    public function destroy(LandingHowItWork $landingHowItWork)
    {
        if ($landingHowItWork->image) {
            unlink_image_by_path($landingHowItWork->image);
        }
        $landingHowItWork->delete();
        return responseJson([], 'Deleted Successfully', 200);
    }
}
