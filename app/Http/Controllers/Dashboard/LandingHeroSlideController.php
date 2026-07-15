<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingHeroSlideRequest;
use App\Http\Resources\Dashboard\LandingHeroSlideResource;
use App\Models\LandingHeroSlide;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingHeroSlideController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing hero read',   only: ['index', 'show']),
            new Middleware('can:landing hero edit',   only: ['store', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $slides = LandingHeroSlide::orderBy('sort_order')->paginate(15);

        return responseJson(
            LandingHeroSlideResource::collection($slides->items()),
            '',
            200,
            getPaginates($slides)
        );
    }

    public function store(LandingHeroSlideRequest $request)
    {
        $data = $this->mapLocalizedFields($request->validated());

        if ($request->hasFile('image')) {
            $data['image'] = store_single_image($request->image);
        }

        LandingHeroSlide::create($data);

        return responseJson([], 'Created Successfully', 200);
    }

    public function show(LandingHeroSlide $landingHeroSlide)
    {
        return responseJson(new LandingHeroSlideResource($landingHeroSlide), 'Data retrieved successfully', 200);
    }

    public function update(LandingHeroSlideRequest $request, LandingHeroSlide $landingHeroSlide)
    {
        $data = $this->mapLocalizedFields($request->validated());

        if ($request->hasFile('image')) {
            if ($landingHeroSlide->image) {
                unlink_image_by_path($landingHeroSlide->image);
            }
            $data['image'] = store_single_image($request->image);
        }

        $landingHeroSlide->update($data);

        return responseJson(new LandingHeroSlideResource($landingHeroSlide), 'Updated Successfully', 200);
    }

    public function destroy(LandingHeroSlide $landingHeroSlide)
    {
        if ($landingHeroSlide->image) {
            unlink_image_by_path($landingHeroSlide->image);
        }

        $landingHeroSlide->delete();

        return responseJson([], 'Deleted Successfully', 200);
    }

    private function mapLocalizedFields(array $data): array
    {
        $data['title'] = [
            'ar' => $data['title_ar'],
            'en' => $data['title_en'],
        ];

        $data['subtitle'] = [
            'ar' => $data['subtitle_ar'] ?? '',
            'en' => $data['subtitle_en'] ?? '',
        ];

        unset($data['title_ar'], $data['title_en'], $data['subtitle_ar'], $data['subtitle_en']);

        return $data;
    }
}
