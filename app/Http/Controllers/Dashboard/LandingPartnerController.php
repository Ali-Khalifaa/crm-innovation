<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingPartnerRequest;
use App\Http\Resources\Dashboard\LandingPartnerResource;
use App\Models\LandingPartner;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingPartnerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing partner read',   only: ['index']),
            new Middleware('can:landing partner create', only: ['store']),
            new Middleware('can:landing partner edit',   only: ['update']),
            new Middleware('can:landing partner delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $partners = LandingPartner::orderBy('sort_order')->orderBy('id')
            ->paginate(20);

        return responseJson(
            LandingPartnerResource::collection($partners),
            'تم بنجاح',
            200,
            getPaginates($partners)
        );
    }

    public function store(LandingPartnerRequest $request)
    {
        $data               = $request->validated();
        $data['image']      = store_single_image($request->file('image'));
        $data['is_active']  = $request->boolean('is_active');

        $partner = LandingPartner::create($data);

        return responseJson(new LandingPartnerResource($partner), 'تمت الإضافة بنجاح', 201);
    }

    public function update(LandingPartnerRequest $request, LandingPartner $partner)
    {
        $data              = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            unlink_image_by_path($partner->image);
            $data['image'] = store_single_image($request->file('image'));
        } else {
            unset($data['image']);
        }

        $partner->update($data);

        return responseJson(new LandingPartnerResource($partner), 'تم التعديل بنجاح');
    }

    public function destroy(LandingPartner $partner)
    {
        unlink_image_by_path($partner->image);
        $partner->delete();

        return responseJson(null, 'تم الحذف بنجاح');
    }
}
