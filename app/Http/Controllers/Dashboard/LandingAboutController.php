<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingAboutRequest;
use App\Http\Resources\Dashboard\LandingAboutResource;
use App\Models\LandingAbout;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingAboutController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [
            new Middleware('can:landing about read', only: ['show']),
            new Middleware('can:landing about edit', only: ['update']),
        ];
    }

    public function show() {
        $about = LandingAbout::firstOrCreate([]);
        return responseJson(new LandingAboutResource($about), 'تم بنجاح');
    }

    public function update(LandingAboutRequest $request) {
        $about = LandingAbout::firstOrCreate([]);
        $data  = $request->validated();
        if ($request->hasFile('image')) {
            unlink_image_by_path($about->image ? 'upload/general/'.$about->image : null);
            $data['image'] = store_single_image($request->file('image'), 'upload/general');
        } else { unset($data['image']); }
        $about->update($data);
        return responseJson(new LandingAboutResource($about), 'تم التعديل بنجاح');
    }
}
