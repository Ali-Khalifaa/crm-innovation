<?php

namespace Modules\Invoices\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Helpers\ApiResponse;
use Modules\Invoices\Http\Requests\StoreInvoiceRequest;
use Modules\Invoices\Http\Requests\UpdateInvoiceRequest;
use Modules\Invoices\Http\Resources\InvoiceResource;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Services\InvoiceService;

class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService) {}

    public function index(): JsonResponse
    {
        $query = Invoice::with(['contact']);

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($search = request('search')) {
            $query->where('invoice_number', 'like', "%{$search}%");
        }

        $invoices = $query->latest()->paginate(15);

        return ApiResponse::paginated($invoices, InvoiceResource::class);
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $tenantId = Auth::guard('tenant_api')->user()->tenant_id;
        $invoice  = $this->invoiceService->create($request->validated(), $tenantId);

        return ApiResponse::success(new InvoiceResource($invoice), __('crm.created'), 201);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load(['contact', 'items']);

        return ApiResponse::success(new InvoiceResource($invoice));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        $invoice = $this->invoiceService->update($invoice, $request->validated());

        return ApiResponse::success(new InvoiceResource($invoice));
    }

    public function destroy(Invoice $invoice): JsonResponse
    {
        $invoice->items()->delete();
        $invoice->delete();

        return ApiResponse::success(null, __('crm.deleted'));
    }

    public function updateStatus(\Illuminate\Http\Request $request, Invoice $invoice): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
        ]);

        $invoice->update(['status' => $data['status']]);
        $invoice->load(['contact', 'items']);

        return ApiResponse::success(new InvoiceResource($invoice), __('crm.updated'));
    }

    public function generatePdf(Invoice $invoice, \Illuminate\Http\Request $request): Response
    {
        // Support token via query string for browser-based PDF download
        if ($request->query('token')) {
            $token = $request->query('token');
            try {
                \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->authenticate();
            } catch (\Exception $e) {
                abort(401, 'Unauthorized');
            }
        }

        $pdf = $this->invoiceService->generatePdf($invoice);

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $invoice->invoice_number . '.pdf"',
        ]);
    }
}
