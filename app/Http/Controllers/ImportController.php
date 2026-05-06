<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function import(Event $event): View
    {
        abort_if($event->user_id !== Auth::id(), 403);

        return view('guests.import', compact('event'));
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        abort_if($event->user_id !== Auth::id(), 403);

        $request->validate([
            'csv_file' => ['required', 'file', 'max:5120'],
        ]);

        [$rows, $errors] = $this->readCsv($request->file('csv_file')->getRealPath());

        if ($errors !== []) {
            return back()->withErrors(['csv_file' => $errors])->withInput();
        }

        $importSeats = collect($rows)->sum('max_seats');

        if ($event->capacity !== null) {
            $allocatedSeats = $event->guests()->sum('max_seats');
            $remainingSeats = $event->capacity - $allocatedSeats;

            if ($importSeats > $remainingSeats) {
                return back()
                    ->withErrors([
                        'csv_file' => "The CSV contains {$importSeats} seats, but only {$remainingSeats} seats remain for this event.",
                    ])
                    ->withInput();
            }
        }

        DB::transaction(function () use ($event, $rows): void {
            foreach ($rows as $row) {
                Guest::create([
                    'event_id' => $event->id,
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'mobile' => $row['mobile'],
                    'max_seats' => $row['max_seats'],
                    'confirmed_seats' => 0,
                    'status' => 'pending',
                    'invite_code' => $this->uniqueInviteCode(),
                ]);
            }
        });

        return redirect()
            ->route('guests.index', $event)
            ->with('success', count($rows).' guests imported successfully.');
    }

    public function sample(Event $event)
    {
        abort_if($event->user_id !== Auth::id(), 403);

        return response()->streamDownload(function (): void {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['name', 'email', 'mobile', 'max_seats']);
            fputcsv($file, ['Jane Guest', 'jane@example.com', '+96170000000', 2]);

            fclose($file);
        }, 'guest-import-sample.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function readCsv(string $path): array
    {
        $file = fopen($path, 'r');

        if ($file === false) {
            return [[], ['Unable to read the uploaded CSV file.']];
        }

        $header = fgetcsv($file);

        if ($header === false) {
            fclose($file);

            return [[], ['The CSV file is empty.']];
        }

        $header = array_map(function ($value): string {
            return Str::of((string) $value)
                ->replace("\xEF\xBB\xBF", '')
                ->trim()
                ->lower()
                ->replace(' ', '_')
                ->toString();
        }, $header);
        $requiredHeaders = ['name'];
        $missingHeaders = array_diff($requiredHeaders, $header);

        if ($missingHeaders !== []) {
            fclose($file);

            return [[], ['Missing required column: '.implode(', ', $missingHeaders).'.']];
        }

        $rows = [];
        $errors = [];
        $line = 1;

        while (($data = fgetcsv($file)) !== false) {
            $line++;

            if ($this->isEmptyRow($data)) {
                continue;
            }

            $row = array_combine($header, array_slice(array_pad($data, count($header), null), 0, count($header)));
            $row = array_map(fn ($value) => trim((string) $value) === '' ? null : $value, $row);

            $validator = Validator::make($row, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'mobile' => ['nullable', 'string', 'max:20'],
                'max_seats' => ['nullable', 'integer', 'min:1'],
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $errors[] = "Line {$line}: {$message}";
                }

                continue;
            }

            $validated = $validator->validated();
            $rows[] = [
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'mobile' => $validated['mobile'] ?? null,
                'max_seats' => (int) ($validated['max_seats'] ?? 1),
            ];
        }

        fclose($file);

        if ($rows === [] && $errors === []) {
            $errors[] = 'The CSV file does not contain any guest rows.';
        }

        return [$rows, $errors];
    }

    private function isEmptyRow(array $row): bool
    {
        return collect($row)->every(fn ($value) => trim((string) $value) === '');
    }

    private function uniqueInviteCode(): string
    {
        do {
            $code = Str::random(10);
        } while (Guest::where('invite_code', $code)->exists());

        return $code;
    }
}
