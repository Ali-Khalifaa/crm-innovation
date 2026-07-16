<?php

namespace Modules\Meetings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Contacts\Models\Contact;
use Modules\Core\Helpers\ApiResponse;
use Modules\Deals\Models\Deal;
use Modules\Meetings\Http\Requests\StoreMeetingRequest;
use Modules\Meetings\Http\Requests\UpdateMeetingRequest;
use Modules\Meetings\Http\Resources\MeetingResource;
use Modules\Meetings\Models\Meeting;

class MeetingController extends Controller
{
    private function resolveMeetable(string $type, int $id)
    {
        return match ($type) {
            'contact' => Contact::findOrFail($id),
            'deal'    => Deal::findOrFail($id),
            default   => abort(422, 'Invalid meetable type'),
        };
    }

    public function index(): JsonResponse
    {
        $query = Meeting::with(['assignee']);

        if ($type = request('meetable_type')) {
            $query->where('meetable_type', $this->morphClass($type));
        }
        if ($id = request('meetable_id')) {
            $query->where('meetable_id', $id);
        }
        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $meetings = $query->orderBy('scheduled_at', 'desc')->paginate(20);

        return ApiResponse::paginated($meetings, MeetingResource::class);
    }

    public function store(StoreMeetingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $meetable = $this->resolveMeetable($data['meetable_type'], $data['meetable_id']);

        $meeting = Meeting::create([
            ...$data,
            'meetable_type' => $meetable::class,
            'meetable_id'   => $meetable->id,
        ]);

        $meeting->load('assignee');

        return ApiResponse::success(new MeetingResource($meeting), __('crm.created'), 201);
    }

    public function show(Meeting $meeting): JsonResponse
    {
        $meeting->load(['assignee', 'meetable']);

        return ApiResponse::success(new MeetingResource($meeting));
    }

    public function update(UpdateMeetingRequest $request, Meeting $meeting): JsonResponse
    {
        $meeting->update($request->validated());
        $meeting->load('assignee');

        return ApiResponse::success(new MeetingResource($meeting));
    }

    public function destroy(Meeting $meeting): JsonResponse
    {
        $meeting->delete();

        return ApiResponse::success(null, __('crm.deleted'));
    }

    private function morphClass(string $type): string
    {
        return match ($type) {
            'contact' => Contact::class,
            'deal'    => Deal::class,
            default   => $type,
        };
    }
}
