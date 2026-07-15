<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingProblemItemRequest;
use App\Http\Resources\Dashboard\LandingProblemItemResource;
use App\Models\LandingProblemItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingProblemItemController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing problem read', only: ['index', 'show']),
            new Middleware('can:landing problem edit', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $items = LandingProblemItem::orderBy('sort_order')->paginate(20);

        return responseJson(
            LandingProblemItemResource::collection($items->items()),
            '',
            200,
            getPaginates($items)
        );
    }

    public function store(LandingProblemItemRequest $request)
    {
        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = store_single_image($request->image);
        }

        LandingProblemItem::create($data);

        return responseJson([], 'Created Successfully', 200);
    }

    public function show(LandingProblemItem $landingProblemItem)
    {
        return responseJson(new LandingProblemItemResource($landingProblemItem), 'Data retrieved successfully', 200);
    }

    public function update(LandingProblemItemRequest $request, LandingProblemItem $landingProblemItem)
    {
        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($landingProblemItem->image) {
                unlink_image_by_path($landingProblemItem->image);
            }
            $data['image'] = store_single_image($request->image);
        }

        $landingProblemItem->update($data);

        return responseJson(new LandingProblemItemResource($landingProblemItem), 'Updated Successfully', 200);
    }

    public function destroy(LandingProblemItem $landingProblemItem)
    {
        if ($landingProblemItem->image) {
            unlink_image_by_path($landingProblemItem->image);
        }

        $landingProblemItem->delete();

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

        $impactAr = trim($data['impact_label_ar'] ?? '');
        $impactEn = trim($data['impact_label_en'] ?? '');

        $data['impact_label'] = ($impactAr || $impactEn)
            ? ['ar' => $impactAr, 'en' => $impactEn]
            : null;

        unset(
            $data['title_ar'],
            $data['title_en'],
            $data['description_ar'],
            $data['description_en'],
            $data['impact_label_ar'],
            $data['impact_label_en']
        );

        return $data;
    }
}
