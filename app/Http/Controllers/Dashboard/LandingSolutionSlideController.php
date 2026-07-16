<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingSolutionSlideRequest;
use App\Http\Resources\Dashboard\LandingSolutionSlideResource;
use App\Models\LandingSolutionSlide;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingSolutionSlideController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing solutions read', only: ['index', 'show']),
            new Middleware('can:landing solutions edit', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $slides = LandingSolutionSlide::orderBy('sort_order')->paginate(15);

        return responseJson(
            LandingSolutionSlideResource::collection($slides->items()),
            '',
            200,
            getPaginates($slides)
        );
    }

    public function store(LandingSolutionSlideRequest $request)
    {
        $data = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = store_single_image($request->image);
        }

        LandingSolutionSlide::create($data);

        return responseJson([], 'Created Successfully', 200);
    }

    public function show(LandingSolutionSlide $landingSolutionSlide)
    {
        return responseJson(new LandingSolutionSlideResource($landingSolutionSlide), 'Data retrieved successfully', 200);
    }

    public function update(LandingSolutionSlideRequest $request, LandingSolutionSlide $landingSolutionSlide)
    {
        $data = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($landingSolutionSlide->image) {
                unlink_image_by_path($landingSolutionSlide->image);
            }
            $data['image'] = store_single_image($request->image);
        }

        $landingSolutionSlide->update($data);

        return responseJson(new LandingSolutionSlideResource($landingSolutionSlide), 'Updated Successfully', 200);
    }

    public function destroy(LandingSolutionSlide $landingSolutionSlide)
    {
        if ($landingSolutionSlide->image) {
            unlink_image_by_path($landingSolutionSlide->image);
        }

        $landingSolutionSlide->delete();

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
        unset($data['icon'], $data['focus_position']);

        return $data;
    }
}
