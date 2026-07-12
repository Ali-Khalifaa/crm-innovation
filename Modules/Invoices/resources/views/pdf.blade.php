<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 13px; color: #1E293B; }
    .invoice-box { max-width: 800px; margin: auto; padding: 30px; }
    .header { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #2563EB; padding-bottom: 20px; }
    .company-name { font-size: 24px; font-weight: bold; color: #2563EB; }
    .invoice-title { font-size: 28px; font-weight: bold; color: #1E293B; text-align: right; }
    .invoice-number { color: #64748B; font-size: 14px; }
    .meta-table { width: 100%; margin-bottom: 25px; }
    .meta-table td { padding: 5px 0; }
    .label { color: #64748B; width: 140px; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
    .badge-paid { background: #D1FAE5; color: #065F46; }
    .badge-sent { background: #DBEAFE; color: #1E40AF; }
    .badge-draft { background: #F1F5F9; color: #475569; }
    .badge-overdue { background: #FEE2E2; color: #991B1B; }
    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    .items-table th { background: #2563EB; color: white; padding: 10px 12px; text-align: left; }
    .items-table td { padding: 10px 12px; border-bottom: 1px solid #E2E8F0; }
    .items-table tr:nth-child(even) td { background: #F8FAFC; }
    .totals { float: right; width: 280px; }
    .totals table { width: 100%; }
    .totals td { padding: 5px 0; }
    .totals .label { color: #64748B; }
    .totals .total-row td { font-size: 16px; font-weight: bold; color: #2563EB; border-top: 2px solid #E2E8F0; padding-top: 8px; }
    .notes { margin-top: 40px; padding: 15px; background: #F8FAFC; border-radius: 6px; color: #64748B; font-size: 12px; }
    .footer { margin-top: 30px; text-align: center; color: #94A3B8; font-size: 11px; border-top: 1px solid #E2E8F0; padding-top: 15px; }
    .text-right { text-align: right; }
</style>
</head>
<body>
<div class="invoice-box">
    <div class="header">
        <div>
            <div class="company-name">{{ $invoice->tenant->name ?? 'CRM Innovation' }}</div>
            <div style="color:#64748B; margin-top:5px;">{{ $invoice->tenant->email ?? '' }}</div>
        </div>
        <div class="text-right">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number"># {{ $invoice->invoice_number }}</div>
        </div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="vertical-align:top; width:50%">
                @if($invoice->contact)
                <strong>Bill To:</strong><br>
                {{ $invoice->contact->full_name }}<br>
                @if($invoice->contact->company) {{ $invoice->contact->company }}<br> @endif
                @if($invoice->contact->email) {{ $invoice->contact->email }}<br> @endif
                @if($invoice->contact->phone) {{ $invoice->contact->phone }} @endif
                @endif
            </td>
            <td style="vertical-align:top; text-align:right;">
                <table>
                    <tr><td class="label">Issue Date:</td><td>{{ $invoice->issue_date->format('M d, Y') }}</td></tr>
                    <tr><td class="label">Due Date:</td><td>{{ $invoice->due_date->format('M d, Y') }}</td></tr>
                    <tr><td class="label">Status:</td><td>
                        <span class="badge badge-{{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span>
                    </td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width:50%">Description</th>
                <th style="width:15%; text-align:right;">Qty</th>
                <th style="width:17%; text-align:right;">Unit Price</th>
                <th style="width:18%; text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                <td class="text-right">${{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">${{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td class="label">Subtotal:</td>
                <td class="text-right">${{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->tax_rate > 0)
            <tr>
                <td class="label">Tax ({{ $invoice->tax_rate }}%):</td>
                <td class="text-right">${{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            @endif
            @if($invoice->discount > 0)
            <tr>
                <td class="label">Discount:</td>
                <td class="text-right">-${{ number_format($invoice->discount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>Total:</td>
                <td class="text-right">${{ number_format($invoice->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="clear:both;"></div>

    @if($invoice->notes)
    <div class="notes">
        <strong>Notes:</strong><br>{{ $invoice->notes }}
    </div>
    @endif

    <div class="footer">
        Generated by CRM Innovation &bull; {{ now()->format('M d, Y') }}
    </div>
</div>
</body>
</html>
