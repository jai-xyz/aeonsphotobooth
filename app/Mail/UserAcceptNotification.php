<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserAcceptNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $checkout_url;

    /**
     * Create a new message instance.
     */
    public function __construct($event, $checkout_url)
    {
        $this->event = $event;
        $this->checkout_url = $checkout_url;
    }

       /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Event Registration Accepted - Complete Your Payment')
                    ->markdown('emails.user_accept_notification', [
                        'event' => $this->event,
                        'checkout_url' => $this->checkout_url,
                    ]);
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'User Notification',
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
