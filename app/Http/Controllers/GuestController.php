<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use chillerlan\QRCode\QRCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\View\View;
use ZipArchive;

class GuestController extends Controller
{
    public function index(Event $event): View
    {
        abort_if($event->user_id !== Auth::id(), 403);
        $guests = $event->guests()->latest()->get();

        return view('guests.index', [
            'event' => $event,
            'guests' => $guests,
        ]);
    }   

    public function export(Event $event)
    {
        abort_if($event->user_id !== Auth::id(), 403);

        $guests = $event->guests()->orderBy('name')->get();
        $eventName = Str::slug($event->name ?: 'event');
        $zipPath = tempnam(sys_get_temp_dir(), 'guest-export-');
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Unable to create guest export.');
        }

        $csv = fopen('php://temp', 'r+');

        fputcsv($csv, [
            'Name',
            'Email',
            'Max Seats',
            'Invite Code',
            'RSVP Link',
            'QR Code File',
        ]);

        foreach ($guests as $guest) {
            $rsvpUrl = route('rsvp.show', ['code' => $guest->invite_code]);
            $qrFileName = 'qr-codes/'.$this->qrFileName($guest);

            fputcsv($csv, [
                $guest->name,
                $guest->email,
                $guest->max_seats,
                $guest->invite_code,
                $rsvpUrl,
                $qrFileName,
            ]);

            $zip->addFromString($qrFileName, $this->qrSvg($rsvpUrl));
        }

        rewind($csv);
        $zip->addFromString('guests.csv', stream_get_contents($csv));
        fclose($csv);

        $zip->close();

        return response()->download($zipPath, "{$eventName}-guests-export.zip")->deleteFileAfterSend();
    }

    public function create(Event $event):View
    {
        $event = auth()->user()->events()->active()->findOrFail($event->id);
        return view('guests.create', compact('event'));    
    }

    public function store(Request $request, Event $event)
    {
        // 1. Verify event ownership and existence
        $event = auth()->user()->events()->findOrFail($event->id);

        // 2. Validate basic input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'max_seats' => 'required|integer|min:1',
            'confirmed_seats' => 'nullable|integer|min:0|lte:max_seats',
            'status' => 'required|in:pending,confirmed',
        ]);

        // 3. Capacity Logic: Check if there is enough room
        if ($event->capacity !== null) {
            // Calculate current total of max_seats already assigned to other guests
            $currentAllocatedSeats = $event->guests()->sum('max_seats');
            $remainingSeats = $event->capacity - $currentAllocatedSeats;

            if ($request->max_seats > $remainingSeats) {
                throw ValidationException::withMessages([
                    'max_seats' => "Only {$remainingSeats} seats remaining. You cannot allocate {$request->max_seats}."
                ]);
            }
        }

        // 4. Auto-generate unique invite code
        $validated['event_id'] = $event->id;
        $validated['invite_code'] = Str::random(10); // Or use a custom format

        // 5. Create the guest
        Guest::create($validated);

        return redirect()->route('guests.index', $event)
                        ->with('success', 'Guest added successfully!');
    }

    public function edit(Event $event, Guest $guest): View
    {
        $this->authorizeGuestAccess($event, $guest);

        return view('guests.edit', compact('event', 'guest'));
    }

    public function update(Request $request, Event $event, Guest $guest)
    {
        $this->authorizeGuestAccess($event, $guest);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:20',
            'max_seats' => 'required|integer|min:1',
            'confirmed_seats' => 'nullable|integer|min:0|lte:max_seats',
            'status' => 'required|in:pending,viewed,confirmed,declined,cancelled',
        ]);

        if ($event->capacity !== null) {
            $allocatedSeats = $event->guests()
                ->whereKeyNot($guest->id)
                ->sum('max_seats');
            $remainingSeats = $event->capacity - $allocatedSeats;

            if ($validated['max_seats'] > $remainingSeats) {
                throw ValidationException::withMessages([
                    'max_seats' => "Only {$remainingSeats} seats are available for this guest.",
                ]);
            }
        }

        if (in_array($validated['status'], ['declined', 'cancelled'], true)) {
            $validated['confirmed_seats'] = 0;
        }

        if (in_array($validated['status'], ['pending', 'viewed'], true)) {
            $validated['responded_at'] = null;
        } elseif ($guest->responded_at === null) {
            $validated['responded_at'] = now();
        }

        $guest->update($validated);

        return redirect()
            ->route('guests.index', $event)
            ->with('success', 'Guest updated successfully.');
    }

    public function destroy(Event $event, Guest $guest)
    {
        $this->authorizeGuestAccess($event, $guest);

        $guest->delete();

        return redirect()
            ->route('guests.index', $event)
            ->with('success', 'Guest deleted successfully.');
    }

    private function qrFileName(Guest $guest): string
    {
        $name = Str::slug($guest->name) ?: 'guest';

        return "{$name}-{$guest->invite_code}.svg";
    }

    private function qrSvg(string $url): string
    {
        $qrCode = (new QRCode())->render($url);

        return base64_decode(Str::after($qrCode, ',')) ?: '';
    }

    private function authorizeGuestAccess(Event $event, Guest $guest): void
    {
        abort_if($event->user_id !== Auth::id(), 403);
        abort_if($guest->event_id !== $event->id, 404);
    }
}
