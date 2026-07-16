<?php

namespace Modules\Invoices\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Helpers\ApiResponse;
use Modules\Invoices\Http\Requests\StorePaymentRequest;
use Modules\Invoices\Http\Resources\InvoicePaymentResource;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\InvoicePayment;

class InvoicePaymentController extends Controller
{
    public function index(Invoice $invoice): JsonResponse
    {
        $payments = $invoice->payments()->with('creator')->latest()->get();

        return ApiResponse::success(InvoicePaymentResource::collection($payments));
    }

    public function store(StorePaymentRequest $request, Invoice $invoice): JsonResponse
    {
        $data = $request->validated();
        $data['invoice_id']  = $invoice->id;
        $data['tenant_id']   = $invoice->tenant_id;
        $data['created_by']  = Auth::guard('tenant_api')->id();

        $payment = InvoicePayment::create($data);

        // Auto-mark invoice as paid when fully paid
        $totalPaid = $invoice->payments()->sum('amount') + 0; // already includes new payment
        if ($totalPaid >= $invoice->total && $invoice->status !== 'paid') {
            $invoice->update(['status' => 'paid']);
        } elseif ($invoice->status === 'draft' || $invoice->status === 'sent') {
            // Keep as sent if partially paid — no status change needed
        }

        $payment->load('creator');

        return ApiResponse::success(new InvoicePaymentResource($payment), __('crm.created'), 201);
    }

    public function destroy(Invoice $invoice, InvoicePayment $payment): JsonResponse
    {
        if ($payment->invoice_id !== $invoice->id) {
            return ApiResponse::error(__('crm.not_found'), 404);
        }

        $payment->delete();

        // Revert status if payments no longer cover full amount
        $totalPaid = $invoice->payments()->sum('amount');
        if ($invoice->status === 'paid' && $totalPaid < $invoice->total) {
            $invoice->update(['status' => 'sent']);
        }

        return ApiResponse::success(null, __('crm.deleted'));
    }
}
