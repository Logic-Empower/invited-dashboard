<?php

namespace App\Mail;

use App\Models\Guest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RsvpResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Guest $guest,
        public string $recipientType,
    ) {
        $this->guest->loadMissing('event.user');
    }

    public function envelope(): Envelope
    {
        $eventName = $this->guest->event->name;

        return new Envelope(
            subject: $this->recipientType === 'owner'
                ? "RSVP {$this->guest->status}: {$this->guest->name}"
                : "Your RSVP for {$eventName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: "emails.rsvp.{$this->recipientType}",
            with: [
                'guest' => $this->guest,
                'event' => $this->guest->event,
                'owner' => $this->guest->event->user,
                'rsvpUrl' => route('rsvp.show', ['code' => $this->guest->invite_code]),
            ],
        );
    }
}
