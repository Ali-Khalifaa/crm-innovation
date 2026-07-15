<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingHeroRequest;
use App\Http\Resources\Dashboard\LandingHeroResource;
use App\Models\LandingHero;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingHeroController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing hero read', only: ['show']),
            new Middleware('can:landing hero edit', only: ['update']),
        ];
    }

    public function show()
    {
        $hero = LandingHero::firstOrCreate([], ['is_active' => true]);

        return responseJson(new LandingHeroResource($hero), 'Data retrieved successfully', 200);
    }

    public function update(LandingHeroRequest $request)
    {
        $hero = LandingHero::firstOrCreate([], ['is_active' => true]);
        $data = $this->parseJsonFields($request->validated());

        if ($request->hasFile('bg_image')) {
            if ($hero->bg_image) {
                unlink_image_by_path($hero->bg_image);
            }
            $data['bg_image'] = store_single_image($request->bg_image);
        }

        if ($request->hasFile('bg_overlay_image')) {
            if ($hero->bg_overlay_image) {
                unlink_image_by_path($hero->bg_overlay_image);
            }
            $data['bg_overlay_image'] = store_single_image($request->bg_overlay_image);
        }

        $hero->update($data);

        return responseJson(new LandingHeroResource($hero), 'Updated Successfully', 200);
    }

    private function parseJsonFields(array $data): array
    {
        foreach (['subtitle', 'headline', 'description', 'btn_primary', 'btn_secondary'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = json_decode($data[$field], true);
            }
        }

        return $data;
    }
}
