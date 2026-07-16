<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $invoice->invoice_number }}</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 20px; color: #1e293b; }
    .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
    .header { background: #2563eb; color: #fff; padding: 28px 32px; }
    .header h1 { margin: 0; font-size: 22px; }
    .header p { margin: 6px 0 0; font-size: 13px; opacity: .85; }
    .body { padding: 28px 32px; }
    .amount-box { background: #f0f9ff; border-left: 4px solid #2563eb; border-radius: 4px; padding: 16px 20px; margin: 20px 0; }
    .amount-box .label { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
    .amount-box .value { font-size: 28px; font-weight: 700; color: #1e293b; margin-top: 4px; }
    .meta-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0; font-size: 13px; }
    .meta-row .lbl { color: #64748b; }
    .footer { background: #f8fafc; padding: 16px 32px; font-size: 12px; color: #94a3b8; text-align: center; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Invoice {{ $invoice->invoice_number }}</h1>
      <p>{{ $invoice->tenant?->name ?? 'CRM Innovation' }}</p>
    </div>
    <div class="body">
      <p>Dear {{ $invoice->contact?->first_name ?? 'Valued Customer' }},</p>
      <p>Please find your invoice attached to this email.</p>

      <div class="amount-box">
        <div class="label">Amount Due</div>
        <div class="value">${{ number_format($invoice->total, 2) }}</div>
      </div>

      <div class="meta-row">
        <span class="lbl">Invoice Number</span>
        <strong>{{ $invoice->invoice_number }}</strong>
      </div>
      <div class="meta-row">
        <span class="lbl">Issue Date</span>
        <span>{{ $invoice->issue_date?->format('M d, Y') }}</span>
      </div>
      <div class="meta-row">
        <span class="lbl">Due Date</span>
        <span>{{ $invoice->due_date?->format('M d, Y') }}</span>
      </div>

      <p style="margin-top: 24px; color: #64748b; font-size: 13px;">
        The PDF invoice is attached. If you have any questions, please don't hesitate to reach out.
      </p>

      <p>Thank you for your business.</p>
    </div>
    <div class="footer">
      {{ $invoice->tenant?->name ?? 'CRM Innovation' }} — Powered by CRM Innovation
    </div>
  </div>
</body>
</html>
