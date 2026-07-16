<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingWhyItemRequest;
use App\Http\Resources\Dashboard\LandingWhyItemResource;
use App\Models\LandingWhyItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingWhyItemController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing why read', only: ['index', 'show']),
            new Middleware('can:landing why edit', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $items = LandingWhyItem::orderBy('sort_order')->paginate(20);

        return responseJson(
            LandingWhyItemResource::collection($items->items()),
            '',
            200,
            getPaginates($items)
        );
    }

    public function store(LandingWhyItemRequest $request)
    {
        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = store_single_image($request->image);
        }

        LandingWhyItem::create($data);

        return responseJson([], 'Created Successfully', 200);
    }

    public function show(LandingWhyItem $landingWhyItem)
    {
        return responseJson(new LandingWhyItemResource($landingWhyItem), 'Data retrieved successfully', 200);
    }

    public function update(LandingWhyItemRequest $request, LandingWhyItem $landingWhyItem)
    {
        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($landingWhyItem->image) {
                unlink_image_by_path($landingWhyItem->image);
            }
            $data['image'] = store_single_image($request->image);
        }

        $landingWhyItem->update($data);

        return responseJson(new LandingWhyItemResource($landingWhyItem), 'Updated Successfully', 200);
    }

    public function destroy(LandingWhyItem $landingWhyItem)
    {
        if ($landingWhyItem->image) {
            unlink_image_by_path($landingWhyItem->image);
        }

        $landingWhyItem->delete();

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
