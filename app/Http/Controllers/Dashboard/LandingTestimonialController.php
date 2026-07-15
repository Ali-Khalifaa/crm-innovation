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
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing testimonial read', only: ['index', 'show']),
            new Middleware('can:landing testimonial edit', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $items = LandingTestimonial::orderBy('sort_order')->paginate(20);

        return responseJson(
            LandingTestimonialResource::collection($items->items()),
            '',
            200,
            getPaginates($items)
        );
    }

    public function store(LandingTestimonialRequest $request)
    {
        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = store_single_image($request->image);
        }

        LandingTestimonial::create($data);

        return responseJson([], 'Created Successfully', 200);
    }

    public function show(LandingTestimonial $landingTestimonial)
    {
        return responseJson(new LandingTestimonialResource($landingTestimonial), 'Data retrieved successfully', 200);
    }

    public function update(LandingTestimonialRequest $request, LandingTestimonial $landingTestimonial)
    {
        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($landingTestimonial->image) {
                unlink_image_by_path($landingTestimonial->image);
            }
            $data['image'] = store_single_image($request->image);
        }

        $landingTestimonial->update($data);

        return responseJson(new LandingTestimonialResource($landingTestimonial), 'Updated Successfully', 200);
    }

    public function destroy(LandingTestimonial $landingTestimonial)
    {
        if ($landingTestimonial->image) {
            unlink_image_by_path($landingTestimonial->image);
        }

        $landingTestimonial->delete();

        return responseJson([], 'Deleted Successfully', 200);
    }

    private function mapLocalizedFields(array $data): array
    {
        $data['name'] = [
            'ar' => $data['name_ar'],
            'en' => $data['name_en'],
        ];

        $data['designation'] = [
            'ar' => $data['designation_ar'],
            'en' => $data['designation_en'],
        ];

        $data['review'] = [
            'ar' => $data['review_ar'],
            'en' => $data['review_en'],
        ];

        unset(
            $data['name_ar'],
            $data['name_en'],
            $data['designation_ar'],
            $data['designation_en'],
            $data['review_ar'],
            $data['review_en']
        );

        return $data;
    }
}
