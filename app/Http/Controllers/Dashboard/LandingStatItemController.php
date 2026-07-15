<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingStatItemRequest;
use App\Http\Resources\Dashboard\LandingStatItemResource;
use App\Models\LandingStatItem;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingStatItemController extends Controller implements HasMiddleware
{
    private const MAX_ITEMS = 4;

    public static function middleware(): array
    {
        return [
            new Middleware('can:landing stat read', only: ['index', 'show']),
            new Middleware('can:landing stat edit', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $items = LandingStatItem::orderBy('sort_order')->paginate(20);

        return responseJson(
            LandingStatItemResource::collection($items->items()),
            '',
            200,
            getPaginates($items)
        );
    }

    public function store(LandingStatItemRequest $request)
    {
        if (LandingStatItem::count() >= self::MAX_ITEMS) {
            return responseJson([], 'لا يمكن إضافة أكثر من ' . self::MAX_ITEMS . ' كروت إحصاء.', 422);
        }

        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = store_single_image($request->image);
        }

        LandingStatItem::create($data);

        return responseJson([], 'Created Successfully', 200);
    }

    public function show(LandingStatItem $landingStatItem)
    {
        return responseJson(new LandingStatItemResource($landingStatItem), 'Data retrieved successfully', 200);
    }

    public function update(LandingStatItemRequest $request, LandingStatItem $landingStatItem)
    {
        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($landingStatItem->image) {
                unlink_image_by_path($landingStatItem->image);
            }
            $data['image'] = store_single_image($request->image);
        }

        $landingStatItem->update($data);

        return responseJson(new LandingStatItemResource($landingStatItem), 'Updated Successfully', 200);
    }

    public function destroy(LandingStatItem $landingStatItem)
    {
        if (LandingStatItem::count() === self::MAX_ITEMS) {
            return responseJson([], 'لا يمكن حذف كروت الإحصاء عند وجود ' . self::MAX_ITEMS . ' كروت.', 422);
        }

        if ($landingStatItem->image) {
            unlink_image_by_path($landingStatItem->image);
        }

        $landingStatItem->delete();

        return responseJson([], 'Deleted Successfully', 200);
    }

    private function mapLocalizedFields(array $data): array
    {
        $data['value'] = [
            'ar' => $data['value_ar'],
            'en' => $data['value_en'],
        ];

        $data['label'] = [
            'ar' => $data['label_ar'],
            'en' => $data['label_en'],
        ];

        $data['suffix'] = $data['suffix'] ?? '';

        unset($data['value_ar'], $data['value_en'], $data['label_ar'], $data['label_en']);

        return $data;
    }
}
