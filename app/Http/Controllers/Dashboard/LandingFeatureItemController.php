<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingFeatureItemRequest;
use App\Http\Resources\Dashboard\LandingFeatureItemResource;
use App\Models\LandingFeatureItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingFeatureItemController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing feature read', only: ['index', 'show']),
            new Middleware('can:landing feature edit', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $items = LandingFeatureItem::orderBy('sort_order')->paginate(20);

        return responseJson(
            LandingFeatureItemResource::collection($items->items()),
            '',
            200,
            getPaginates($items)
        );
    }

    public function store(LandingFeatureItemRequest $request)
    {
        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = store_single_image($request->image);
        }

        LandingFeatureItem::create($data);

        return responseJson([], 'Created Successfully', 200);
    }

    public function show(LandingFeatureItem $landingFeatureItem)
    {
        return responseJson(new LandingFeatureItemResource($landingFeatureItem), 'Data retrieved successfully', 200);
    }

    public function update(LandingFeatureItemRequest $request, LandingFeatureItem $landingFeatureItem)
    {
        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($landingFeatureItem->image) {
                unlink_image_by_path($landingFeatureItem->image);
            }
            $data['image'] = store_single_image($request->image);
        }

        $landingFeatureItem->update($data);

        return responseJson(new LandingFeatureItemResource($landingFeatureItem), 'Updated Successfully', 200);
    }

    public function destroy(LandingFeatureItem $landingFeatureItem)
    {
        if ($landingFeatureItem->image) {
            unlink_image_by_path($landingFeatureItem->image);
        }

        $landingFeatureItem->delete();

        return responseJson([], 'Deleted Successfully', 200);
    }

    private function mapLocalizedFields(array $data): array
    {
        $data['title'] = [
            'ar' => $data['title_ar'],
            'en' => $data['title_en'],
        ];

        $data['description'] = [
            'ar' => $data['description_ar'],
            'en' => $data['description_en'],
        ];

        unset($data['title_ar'], $data['title_en'], $data['description_ar'], $data['description_en']);

        return $data;
    }
}
