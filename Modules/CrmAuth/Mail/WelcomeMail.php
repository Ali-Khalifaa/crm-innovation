<?php

namespace Modules\CrmAuth\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Modules\CrmAuth\Models\User;
use Modules\CrmAuth\Models\Tenant;

class WelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User   $user,
        public readonly Tenant $tenant
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to CRM Innovation — ' . $this->tenant->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'crmauth::emails.welcome',
        );
    }
}
