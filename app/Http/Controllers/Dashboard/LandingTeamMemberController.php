<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LandingTeamMemberRequest;
use App\Http\Resources\Dashboard\LandingTeamMemberResource;
use App\Models\LandingTeamMember;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingTeamMemberController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [
            new Middleware('can:landing team read',   only: ['index']),
            new Middleware('can:landing team create', only: ['store']),
            new Middleware('can:landing team edit',   only: ['update']),
            new Middleware('can:landing team delete', only: ['destroy']),
        ];
    }

    public function index(Request $request) {
        $items = LandingTeamMember::orderBy('sort_order')->orderBy('id')->paginate(15);
        return responseJson(LandingTeamMemberResource::collection($items), 'تم بنجاح', 200, getPaginates($items));
    }

    public function store(LandingTeamMemberRequest $request) {
        $data = $request->validated();
        if ($request->hasFile('photo'))
            $data['photo'] = store_single_image($request->file('photo'), 'upload/general');
        $item = LandingTeamMember::create($data);
        return responseJson(new LandingTeamMemberResource($item), 'تمت الإضافة بنجاح', 201);
    }

    public function update(LandingTeamMemberRequest $request, LandingTeamMember $landingTeamMember) {
        $data = $request->validated();
        if ($request->hasFile('photo')) {
            unlink_image_by_path($landingTeamMember->photo ? 'upload/general/'.$landingTeamMember->photo : null);
            $data['photo'] = store_single_image($request->file('photo'), 'upload/general');
        } else { unset($data['photo']); }
        $landingTeamMember->update($data);
        return responseJson(new LandingTeamMemberResource($landingTeamMember), 'تم التعديل بنجاح');
    }

    public function destroy(LandingTeamMember $landingTeamMember) {
        unlink_image_by_path($landingTeamMember->photo ? 'upload/general/'.$landingTeamMember->photo : null);
        $landingTeamMember->delete();
        return responseJson(null, 'تم الحذف بنجاح');
    }
}
