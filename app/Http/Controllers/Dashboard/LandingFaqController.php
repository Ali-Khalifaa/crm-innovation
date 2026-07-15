<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingFaqRequest;
use App\Http\Resources\Dashboard\LandingFaqResource;
use App\Models\LandingFaq;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingFaqController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:landing faq read', only: ['index', 'show']),
            new Middleware('can:landing faq edit', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $items = LandingFaq::orderBy('sort_order')->paginate(20);

        return responseJson(
            LandingFaqResource::collection($items->items()),
            '',
            200,
            getPaginates($items)
        );
    }

    public function store(LandingFaqRequest $request)
    {
        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        LandingFaq::create($data);

        return responseJson([], 'Created Successfully', 200);
    }

    public function show(LandingFaq $landingFaq)
    {
        return responseJson(new LandingFaqResource($landingFaq), 'Data retrieved successfully', 200);
    }

    public function update(LandingFaqRequest $request, LandingFaq $landingFaq)
    {
        $data              = $this->mapLocalizedFields($request->validated());
        $data['is_active'] = $request->boolean('is_active');

        $landingFaq->update($data);

        return responseJson(new LandingFaqResource($landingFaq), 'Updated Successfully', 200);
    }

    public function destroy(LandingFaq $landingFaq)
    {
        $landingFaq->delete();

        return responseJson([], 'Deleted Successfully', 200);
    }

    private function mapLocalizedFields(array $data): array
    {
        $data['question'] = [
            'ar' => $data['question_ar'],
            'en' => $data['question_en'],
        ];

        $data['answer'] = [
            'ar' => $data['answer_ar'],
            'en' => $data['answer_en'],
        ];

        unset($data['question_ar'], $data['question_en'], $data['answer_ar'], $data['answer_en']);

        return $data;
    }
}
