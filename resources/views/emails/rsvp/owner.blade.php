<p>Hello {{ $owner->name }},</p>

<p>{{ $guest->name }} has submitted an RSVP for {{ $event->name }}.</p>

<p>
    Status: <strong>{{ ucfirst($guest->status) }}</strong><br>
    Confirmed seats: <strong>{{ $guest->confirmed_seats }}</strong><br>
    Maximum seats: <strong>{{ $guest->max_seats }}</strong><br>
    Email: <strong>{{ $guest->email ?? '-' }}</strong><br>
    Mobile: <strong>{{ $guest->mobile ?? '-' }}</strong><br>
    Invite code: <strong>{{ $guest->invite_code }}</strong>
</p>

<p>
    RSVP link:<br>
    <a href="{{ $rsvpUrl }}">{{ $rsvpUrl }}</a>
</p>
