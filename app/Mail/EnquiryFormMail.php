<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnquiryFormMail extends Mailable
{
    use Queueable, SerializesModels;
    public $formData;
    public $isAdmin;

    /**
     * Create a new message instance.
     */
    public function __construct($formData, $isAdmin = false)
    {
        $this->formData = $formData;
        $this->isAdmin = $isAdmin;
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enquiry Form Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.enquiry-form',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Build the message.
     */
    public function build(){
        return $this->subject('Enquiry Form Mail')
            ->markdown('emails.enquiry-form')
            ->with([
                'formData' => $this->formData,
                'isAdmin' => $this->isAdmin
            ]);
    }
}
