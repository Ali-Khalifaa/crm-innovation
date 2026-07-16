<?php

namespace Modules\Invoices\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Services\InvoiceService;

class InvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Invoice $invoice) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('crm.invoice_email_subject', ['number' => $this->invoice->invoice_number]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'invoices::emails.invoice',
        );
    }

    public function attachments(): array
    {
        try {
            $service = app(InvoiceService::class);
            $pdf     = $service->generatePdf($this->invoice);

            return [
                \Illuminate\Mail\Mailables\Attachment::fromData(
                    fn() => $pdf,
                    $this->invoice->invoice_number . '.pdf'
                )->withMime('application/pdf'),
            ];
        } catch (\Exception) {
            return [];
        }
    }
}
