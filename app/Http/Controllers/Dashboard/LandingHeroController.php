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
        $hero = LandingHero::firstOrCreate([]);
        return responseJson(new LandingHeroResource($hero), 'Data retrieved successfully', 200);
    }

    public function update(LandingHeroRequest $request)
    {
        $hero = LandingHero::firstOrCreate([]);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($hero->image) {
                unlink_image_by_path($hero->image);
            }
            $data['image'] = store_single_image($request->image);
        }

        $hero->update($data);
        return responseJson(new LandingHeroResource($hero), 'Updated Successfully', 200);
    }
}
