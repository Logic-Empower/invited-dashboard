<?php

namespace App\Http\Controllers;

use App\Mail\RsvpResponseMail;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RSVPController extends Controller
{
    public function show(string $code): View
    {
        $guest = Guest::with('event')
            ->where('invite_code', $code)
            ->firstOrFail();

        if ($guest->status === 'pending') {
            $guest->update([
                'status' => 'viewed',
                'viewed_at' => $guest->viewed_at ?? now(),
            ]);
        } elseif ($guest->viewed_at === null) {
            $guest->update(['viewed_at' => now()]);
        }

        return view('rsvp.show', [
            'guest' => $guest->fresh('event'),
        ]);
    }

    public function respond(Request $request, string $code)
    {
        $guest = Guest::where('invite_code', $code)->firstOrFail();

        if ($guest->responded_at !== null || ! in_array($guest->status, ['pending', 'viewed'], true)) {
            return redirect()
                ->route('rsvp.show', ['code' => $guest->invite_code])
                ->with('info', 'Your RSVP has already been submitted.');
        }

        $validated = $request->validate([
            'response' => ['required', Rule::in(['confirmed', 'declined'])],
            'confirmed_seats' => [
                Rule::requiredIf($request->input('response') === 'confirmed'),
                'nullable',
                'integer',
                'min:1',
                'max:'.$guest->max_seats,
            ],
        ]);

        if ($validated['response'] === 'declined') {
            $guest->update([
                'status' => 'declined',
                'confirmed_seats' => 0,
                'responded_at' => now(),
            ]);

            $this->sendConfirmationEmails($guest->fresh('event.user'));

            return redirect()
                ->route('rsvp.show', ['code' => $guest->invite_code])
                ->with('success', 'Thank you. Your RSVP has been declined.');
        }

        $guest->update([
            'status' => 'confirmed',
            'confirmed_seats' => $validated['confirmed_seats'],
            'responded_at' => now(),
        ]);

        $this->sendConfirmationEmails($guest->fresh('event.user'));

        return redirect()
            ->route('rsvp.show', ['code' => $guest->invite_code])
            ->with('success', 'Thank you. Your RSVP has been confirmed.');
    }

    private function sendConfirmationEmails(Guest $guest): void
    {
        Mail::to($guest->event->user->email)->send(new RsvpResponseMail($guest, 'owner'));

        if ($guest->email) {
            Mail::to($guest->email)->send(new RsvpResponseMail($guest, 'guest'));
        }
    }
}
