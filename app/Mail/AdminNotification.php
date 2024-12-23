<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;


class AdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $userRegistration;
    public $adminUrl;
    
    /**
     * Create a new message instance.
     */
    public function __construct($userRegistration)
    {
        $this->userRegistration = $userRegistration;
        $this->adminUrl = URL::route('event.index', ['id' => $userRegistration->id]);
    }

       /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->subject('You’ve Got a New Photobooth Event Registration!')
                    ->markdown('emails.admin_notification',
                [
                    'userRegistration' => $this->userRegistration,
                    'adminUrl' => $this->adminUrl,
                ]);
                   
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Admin Notification',
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
    // public function attachments(): array
    // {
    //     return [];
    // }
}
