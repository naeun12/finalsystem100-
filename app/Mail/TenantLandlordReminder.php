<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantLandlordReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $recipientName;
    public $messageBody;
    public $type; // 'tenant' or 'landlord'

    /**
     * Create a new message instance.
     */
    public function __construct($recipientName, $messageBody, $type = 'tenant')
    {
        $this->recipientName = $recipientName;
        $this->messageBody = $messageBody;
        $this->type = $type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->type === 'tenant' ? 'Tenant Reminder' : 'Landlord Reminder',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.reminder'
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
