<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingTestimonialRequest;
use App\Http\Resources\Dashboard\LandingTestimonialResource;
use App\Models\LandingTestimonial;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingTestimonialController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [
            new Middleware('can:landing testimonial read',   only: ['index']),
            new Middleware('can:landing testimonial create', only: ['store']),
            new Middleware('can:landing testimonial edit',   only: ['update']),
            new Middleware('can:landing testimonial delete', only: ['destroy']),
        ];
    }

    public function index(Request $request) {
        $items = LandingTestimonial::orderBy('sort_order')->orderBy('id')->paginate(15);
        return responseJson(LandingTestimonialResource::collection($items), 'تم بنجاح', 200, getPaginates($items));
    }

    public function store(LandingTestimonialRequest $request) {
        $data = $request->validated();
        if ($request->hasFile('photo'))
            $data['photo'] = store_single_image($request->file('photo'), 'upload/general');
        $item = LandingTestimonial::create($data);
        return responseJson(new LandingTestimonialResource($item), 'تمت الإضافة بنجاح', 201);
    }

    public function update(LandingTestimonialRequest $request, LandingTestimonial $testimonial) {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            unlink_image_by_path($testimonial->photo ? 'upload/general/'.$testimonial->photo : null);
            $data['photo'] = store_single_image($request->file('photo'), 'upload/general');
        } else { unset($data['photo']); }
        $testimonial->update($data);
        return responseJson(new LandingTestimonialResource($testimonial), 'تم التعديل بنجاح');
    }

    public function destroy(LandingTestimonial $testimonial) {
        unlink_image_by_path($testimonial->photo ? 'upload/general/'.$testimonial->photo : null);
        $testimonial->delete();
        return responseJson(null, 'تم الحذف بنجاح');
    }
}
