<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingBlogPostRequest;
use App\Http\Resources\Dashboard\LandingBlogPostResource;
use App\Models\LandingBlogPost;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingBlogPostController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [
            new Middleware('can:landing blog read',   only: ['index']),
            new Middleware('can:landing blog create', only: ['store']),
            new Middleware('can:landing blog edit',   only: ['update']),
            new Middleware('can:landing blog delete', only: ['destroy']),
        ];
    }

    public function index(Request $request) {
        $items = LandingBlogPost::orderBy('sort_order')->orderByDesc('published_at')->paginate(15);
        return responseJson(LandingBlogPostResource::collection($items), 'تم بنجاح', 200, getPaginates($items));
    }

    public function store(LandingBlogPostRequest $request) {
        $data = $request->validated();
        if ($request->hasFile('image'))
            $data['image'] = store_single_image($request->file('image'), 'upload/general');
        $item = LandingBlogPost::create($data);
        return responseJson(new LandingBlogPostResource($item), 'تمت الإضافة بنجاح', 201);
    }

    public function update(LandingBlogPostRequest $request, LandingBlogPost $landingBlogPost) {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            unlink_image_by_path($landingBlogPost->image ? 'upload/general/'.$landingBlogPost->image : null);
            $data['image'] = store_single_image($request->file('image'), 'upload/general');
        } else { unset($data['image']); }
        $landingBlogPost->update($data);
        return responseJson(new LandingBlogPostResource($landingBlogPost), 'تم التعديل بنجاح');
    }

    public function destroy(LandingBlogPost $landingBlogPost) {
        unlink_image_by_path($landingBlogPost->image ? 'upload/general/'.$landingBlogPost->image : null);
        $landingBlogPost->delete();
        return responseJson(null, 'تم الحذف بنجاح');
    }
}
