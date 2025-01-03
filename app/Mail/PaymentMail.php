<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class PaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    
    /**
     * Create a new message instance.
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    public function build()
    {
        return $this->subject('Event Registration Listed - Payment Successfully Received')
        ->markdown('emails.payment_success_notification', [
            'event' => $this->event,
        ]);
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Payment Mail',
    //     );
    // }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
