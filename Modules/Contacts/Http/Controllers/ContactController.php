<?php

namespace Modules\Contacts\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $this->authorize('viewAny', Contact::class);

        $query = Contact::with(['creator', 'companyRecord']);

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

        if ($source = request('lead_source')) {
            $query->where('lead_source', $source);
        }

        if ($companyId = request('company_id')) {
            $query->where('company_id', $companyId);
        }

        $contacts = $query->latest()->paginate(15);

        return ApiResponse::paginated($contacts, ContactResource::class);
    }

    public function store(StoreContactRequest $request): JsonResponse
    {
        $this->authorize('create', Contact::class);

        $data = $request->validated();
        $data['created_by'] = Auth::guard('tenant_api')->id();

        $contact = Contact::create($data);
        CrmActivity::log('contact_created', $contact, "Created contact: {$contact->first_name} {$contact->last_name}");

        return ApiResponse::success(new ContactResource($contact), __('crm.created'), 201);
    }

    public function show(Contact $contact): JsonResponse
    {
        $this->authorize('view', $contact);

        $contact->load([
            'creator',
            'companyRecord',
            'owner',
            'deals.stage',
            'tasks.assignee',
        ]);

        return ApiResponse::success(new ContactResource($contact));
    }

    public function update(UpdateContactRequest $request, Contact $contact): JsonResponse
    {
        $this->authorize('update', $contact);

        $contact->update($request->validated());
        CrmActivity::log('contact_updated', $contact, "Updated contact: {$contact->first_name} {$contact->last_name}");

        return ApiResponse::success(new ContactResource($contact));
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $this->authorize('delete', $contact);

        $contact->delete();

        return ApiResponse::success(null, __('crm.deleted'));
    }

    public function activities(Contact $contact): JsonResponse
    {
        $this->authorize('view', $contact);

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

    public function bulkAction(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Contact::class);

        $request->validate([
            'action' => 'required|in:delete,change_status',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer',
            'status' => 'required_if:action,change_status|in:lead,customer,inactive',
        ]);

        $user = auth('tenant_api')->user();
        $contacts = Contact::whereIn('id', $request->ids)
            ->where('tenant_id', $user->tenant_id)
            ->get();

        $count = $contacts->count();

        if ($request->action === 'delete') {
            Contact::whereIn('id', $contacts->pluck('id'))->delete();
            return ApiResponse::success(['affected' => $count], __('crm.bulk_deleted', ['count' => $count]));
        }

        if ($request->action === 'change_status') {
            Contact::whereIn('id', $contacts->pluck('id'))->update(['status' => $request->status]);
            return ApiResponse::success(['affected' => $count], __('crm.bulk_updated', ['count' => $count]));
        }

        return ApiResponse::error(__('crm.invalid_action'));
    }

    public function import(Request $request): JsonResponse
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:5120']);

        $user = auth('tenant_api')->user();
        $import = new \Modules\Contacts\Imports\ContactImport($user->tenant_id, $user->id);
        Excel::import($import, $request->file('file'));

        $count = $import->getImportedCount();
        $errors = $import->getErrors();

        return ApiResponse::success(
            ['imported' => $count, 'errors' => $errors],
            __('crm.import_done', ['count' => $count])
        );
    }

    public function export(Request $request)
    {
        if ($token = $request->query('token')) {
            try {
                \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->authenticate();
            } catch (\Exception) {
                abort(401);
            }
        }

        $tenantId = Auth::guard('tenant_api')->user()->tenant_id;
        $filename = 'contacts-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new ContactsExport($tenantId), $filename);
    }
}
