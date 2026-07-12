<?php

namespace Modules\Invoices\Services;

use Illuminate\Support\Facades\DB;
use Modules\Invoices\Models\Invoice;

class InvoiceService
{
    public function create(array $data, int $tenantId): Invoice
    {
        return DB::transaction(function () use ($data, $tenantId) {
            $items    = $data['items'];
            $subtotal = collect($items)->sum(fn($i) => $i['quantity'] * $i['unit_price']);
            $taxRate  = $data['tax_rate'] ?? 0;
            $discount = $data['discount'] ?? 0;
            $taxAmount = round($subtotal * $taxRate / 100, 2);
            $total    = $subtotal + $taxAmount - $discount;

            $invoice = Invoice::create([
                'tenant_id'      => $tenantId,
                'invoice_number' => $this->generateNumber($tenantId),
                'contact_id'     => $data['contact_id'] ?? null,
                'issue_date'     => $data['issue_date'],
                'due_date'       => $data['due_date'],
                'status'         => $data['status'] ?? 'draft',
                'subtotal'       => $subtotal,
                'tax_rate'       => $taxRate,
                'tax_amount'     => $taxAmount,
                'discount'       => $discount,
                'total'          => $total,
                'notes'          => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                    'total'       => round($item['quantity'] * $item['unit_price'], 2),
                ]);
            }

            return $invoice->load(['contact', 'items']);
        });
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            if (isset($data['items'])) {
                $items    = $data['items'];
                $subtotal = collect($items)->sum(fn($i) => $i['quantity'] * $i['unit_price']);
                $taxRate  = $data['tax_rate'] ?? $invoice->tax_rate;
                $discount = $data['discount'] ?? $invoice->discount;
                $taxAmount = round($subtotal * $taxRate / 100, 2);
                $total    = $subtotal + $taxAmount - $discount;

                $data['subtotal']   = $subtotal;
                $data['tax_amount'] = $taxAmount;
                $data['total']      = $total;
                $data['tax_rate']   = $taxRate;
                $data['discount']   = $discount;

                $invoice->items()->delete();

                foreach ($items as $item) {
                    $invoice->items()->create([
                        'description' => $item['description'],
                        'quantity'    => $item['quantity'],
                        'unit_price'  => $item['unit_price'],
                        'total'       => round($item['quantity'] * $item['unit_price'], 2),
                    ]);
                }

                unset($data['items']);
            }

            $invoice->update($data);

            return $invoice->load(['contact', 'items']);
        });
    }

    public function generatePdf(Invoice $invoice): string
    {
        $invoice->load(['contact', 'items', 'tenant.plan']);

        $pdf = \PDF::loadView('invoices::pdf', ['invoice' => $invoice]);

        return $pdf->output();
    }

    private function generateNumber(int $tenantId): string
    {
        $last = Invoice::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->orderBy('id', 'desc')
            ->value('invoice_number');

        if ($last) {
            $num = (int) substr($last, 4) + 1;
        } else {
            $num = 1;
        }

        return 'INV-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }
}
