<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingHowItemRequest;
use App\Http\Resources\Dashboard\LandingHowItemResource;
use App\Models\LandingHowItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingHowItemController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing how it works read', only: ['index', 'show']),
            new Middleware('can:landing how it works edit', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $items = LandingHowItem::orderBy('sort_order')->paginate(20);

        return responseJson(
            LandingHowItemResource::collection($items->items()),
            '',
            200,
            getPaginates($items)
        );
    }

    public function store(LandingHowItemRequest $request)
    {
        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = store_single_image($request->image);
        }

        LandingHowItem::create($data);

        return responseJson([], 'Created Successfully', 200);
    }

    public function show(LandingHowItem $landingHowItem)
    {
        return responseJson(new LandingHowItemResource($landingHowItem), 'Data retrieved successfully', 200);
    }

    public function update(LandingHowItemRequest $request, LandingHowItem $landingHowItem)
    {
        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($landingHowItem->image) {
                unlink_image_by_path($landingHowItem->image);
            }
            $data['image'] = store_single_image($request->image);
        }

        $landingHowItem->update($data);

        return responseJson(new LandingHowItemResource($landingHowItem), 'Updated Successfully', 200);
    }

    public function destroy(LandingHowItem $landingHowItem)
    {
        if ($landingHowItem->image) {
            unlink_image_by_path($landingHowItem->image);
        }

        $landingHowItem->delete();

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
