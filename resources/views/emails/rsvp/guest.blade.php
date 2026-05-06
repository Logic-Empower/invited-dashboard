<p>Hello {{ $guest->name }},</p>

<p>Thank you for responding to {{ $event->name }}.</p>

@if($guest->status === 'confirmed')
    <p>
        Your RSVP is confirmed for
        <strong>{{ $guest->confirmed_seats }} {{ Str::plural('seat', $guest->confirmed_seats) }}</strong>.
    </p>
@else
    <p>Your RSVP has been declined.</p>
@endif

@if($event->event_date)
    <p>Event date: <strong>{{ $event->event_date->format('M d, Y') }}</strong></p>
@endif

<p>
    Your RSVP link:<br>
    <a href="{{ $rsvpUrl }}">{{ $rsvpUrl }}</a>
</p>
