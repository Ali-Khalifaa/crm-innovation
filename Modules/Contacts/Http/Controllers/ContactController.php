<?php

namespace Modules\Contacts\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Contacts\Exports\ContactsExport;
use Modules\Contacts\Http\Requests\StoreContactRequest;
use Modules\Contacts\Http\Requests\UpdateContactRequest;
use Modules\Contacts\Http\Resources\ContactResource;
use Modules\Contacts\Models\Contact;
use Modules\Core\Helpers\ApiResponse;
use Modules\Core\Models\CrmActivity;

class ContactController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Contact::query();

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $contacts = $query->latest()->paginate(15);

        return ApiResponse::paginated($contacts, ContactResource::class);
    }

    public function store(StoreContactRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = Auth::guard('tenant_api')->id();

        $contact = Contact::create($data);
        CrmActivity::log('contact_created', $contact, "Created contact: {$contact->first_name} {$contact->last_name}");

        return ApiResponse::success(new ContactResource($contact), __('crm.created'), 201);
    }

    public function show(Contact $contact): JsonResponse
    {
        return ApiResponse::success(new ContactResource($contact));
    }

    public function update(UpdateContactRequest $request, Contact $contact): JsonResponse
    {
        $contact->update($request->validated());
        CrmActivity::log('contact_updated', $contact, "Updated contact: {$contact->first_name} {$contact->last_name}");

        return ApiResponse::success(new ContactResource($contact));
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $name = $contact->first_name . ' ' . $contact->last_name;
        $contact->delete();
        // Note: activity logged before delete

        return ApiResponse::success(null, __('crm.deleted'));
    }

    public function activities(Contact $contact): JsonResponse
    {
        $activities = CrmActivity::with('user')
            ->where('subject_type', Contact::class)
            ->where('subject_id', $contact->id)
            ->latest()
            ->limit(20)
            ->get();

        return ApiResponse::success($activities->map(fn($a) => [
            'id'          => $a->id,
            'action'      => $a->action,
            'description' => $a->description,
            'user'        => $a->user?->name,
            'created_at'  => $a->created_at,
        ]));
    }

    public function export(\Illuminate\Http\Request $request)
    {
        if ($token = $request->query('token')) {
            try {
                \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->authenticate();
            } catch (\Exception $e) {
                abort(401);
            }
        }

        $tenantId = Auth::guard('tenant_api')->user()->tenant_id;
        $filename = 'contacts-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new ContactsExport($tenantId), $filename);
    }
}
