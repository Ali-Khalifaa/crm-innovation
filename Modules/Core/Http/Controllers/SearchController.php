<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Companies\Models\Company;
use Modules\Contacts\Models\Contact;
use Modules\Core\Helpers\ApiResponse;
use Modules\Deals\Models\Deal;

class SearchController extends Controller
{
    public function search(): JsonResponse
    {
        $q        = trim(request('q', ''));
        $limit    = min((int) request('limit', 5), 10);
        $tenantId = Auth::guard('tenant_api')->user()->tenant_id;

        if (strlen($q) < 2) {
            return ApiResponse::success(['contacts' => [], 'deals' => [], 'companies' => []]);
        }

        $contacts = Contact::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where(fn($query) => $query
                ->where('first_name', 'like', "%{$q}%")
                ->orWhere('last_name',  'like', "%{$q}%")
                ->orWhere('email',      'like', "%{$q}%")
                ->orWhere('phone',      'like', "%{$q}%")
            )
            ->select('id', 'first_name', 'last_name', 'email', 'phone', 'status')
            ->limit($limit)->get()
            ->map(fn($c) => [
                'type'     => 'contact',
                'id'       => $c->id,
                'label'    => "{$c->first_name} {$c->last_name}",
                'sub'      => $c->email,
                'status'   => $c->status,
                'url'      => "/crm/contacts/{$c->id}",
            ]);

        $deals = Deal::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('title', 'like', "%{$q}%")
            ->select('id', 'title', 'value', 'status')
            ->limit($limit)->get()
            ->map(fn($d) => [
                'type'   => 'deal',
                'id'     => $d->id,
                'label'  => $d->title,
                'sub'    => '$' . number_format($d->value, 2),
                'status' => $d->status,
                'url'    => '/crm/deals',
            ]);

        $companies = Company::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where(fn($query) => $query
                ->where('name',     'like', "%{$q}%")
                ->orWhere('email',    'like', "%{$q}%")
                ->orWhere('industry', 'like', "%{$q}%")
            )
            ->select('id', 'name', 'industry', 'status')
            ->limit($limit)->get()
            ->map(fn($c) => [
                'type'     => 'company',
                'id'       => $c->id,
                'label'    => $c->name,
                'sub'      => $c->industry,
                'status'   => $c->status,
                'url'      => "/crm/companies/{$c->id}",
            ]);

        return ApiResponse::success([
            'contacts'  => $contacts,
            'deals'     => $deals,
            'companies' => $companies,
        ]);
    }
}
