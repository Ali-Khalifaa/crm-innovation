<?php

namespace Modules\Companies\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Companies\Http\Requests\StoreCompanyRequest;
use Modules\Companies\Http\Requests\UpdateCompanyRequest;
use Modules\Companies\Http\Resources\CompanyResource;
use Modules\Companies\Models\Company;
use Modules\Core\Helpers\ApiResponse;

class CompanyController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Company::class);

        $query = Company::withCount(['contacts', 'deals']);

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('industry', 'like', "%{$search}%");
            });
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $companies = $query->latest()->paginate(15);

        return ApiResponse::paginated($companies, CompanyResource::class);
    }

    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $this->authorize('create', Company::class);

        $data = $request->validated();
        $data['created_by'] = Auth::guard('tenant_api')->id();

        $company = Company::create($data);

        return ApiResponse::success(new CompanyResource($company), __('crm.created'), 201);
    }

    public function show(Company $company): JsonResponse
    {
        $this->authorize('view', $company);

        $company->load([
            'contacts' => fn($q) => $q->latest()->limit(20),
            'deals'    => fn($q) => $q->with('stage')->latest()->limit(20),
        ])->loadCount(['contacts', 'deals']);

        return ApiResponse::success(new CompanyResource($company));
    }

    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $this->authorize('update', $company);

        $company->update($request->validated());

        return ApiResponse::success(new CompanyResource($company));
    }

    public function destroy(Company $company): JsonResponse
    {
        $this->authorize('delete', $company);

        $company->delete();

        return ApiResponse::success(null, __('crm.deleted'));
    }

    public function dropdown(): JsonResponse
    {
        $companies = Company::select('id', 'name')->orderBy('name')->get();

        return ApiResponse::success($companies);
    }
}
