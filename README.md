# Invited Dashboard

Invited Dashboard is a Laravel application for managing event invitations, guests, RSVP links, QR codes, imports, exports, and RSVP confirmation emails.

The app lets an event owner create events, add or import guests, generate unique invite links and QR codes, export guest data for printing, and collect final RSVP responses from public invite pages.

## Tech Stack

- PHP 8.2+
- Laravel 12
- Laravel Breeze authentication
- Blade views
- Tailwind CSS
- Alpine.js
- Vite
- Filament 4
- chillerlan/php-qrcode for QR code generation

## Core Features

- User authentication and profile management
- Event CRUD
- Event capacity tracking
- Guest CRUD
- Automatic unique invite code generation
- Public RSVP page at `/invite/{code}`
- RSVP status lifecycle: `pending`, `viewed`, `confirmed`, `declined`, `cancelled`
- Copyable invite codes
- QR code preview and SVG download per guest
- Guest CSV import with sample CSV download
- Guest ZIP export containing `guests.csv` and individual QR SVG files
- RSVP confirmation emails to the event owner and guest

## Installation

Clone the project and install dependencies:

```bash
composer install
npm install
```

Create your environment file:

```bash
cp .env.example .env
php artisan key:generate
```

Configure the database in `.env`. The default example uses SQLite:

```env
DB_CONNECTION=sqlite
```

If using SQLite, create the database file if needed:

```bash
touch database/database.sqlite
```

Run migrations:

```bash
php artisan migrate
```

Build frontend assets:

```bash
npm run build
```

For local development, run:

```bash
composer run dev
```

This starts the Laravel server, queue listener, logs, and Vite dev server together.

## Environment

Important `.env` values:

```env
APP_NAME="Invited Dashboard"
APP_URL=http://localhost:8000

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

For real RSVP emails, replace `MAIL_MAILER=log` with a real mail driver configuration such as SMTP, Mailgun, Postmark, SES, or another Laravel-supported mailer.

## Main Models

### User

Owns events. Event access is scoped to the authenticated owner.

### Event

Fields include:

- `user_id`
- `name`
- `description`
- `capacity`
- `event_date`
- `expires_at`

Events support soft deletes.

Useful computed behavior:

- `occupied_seats`: confirmed seats for confirmed guests plus max seats for pending guests
- `remaining_seats`: event capacity minus occupied seats, or `null` for unlimited capacity

### Guest

Fields include:

- `event_id`
- `name`
- `mobile`
- `email`
- `max_seats`
- `confirmed_seats`
- `status`
- `invite_code`
- `viewed_at`
- `responded_at`

Guests support soft deletes.

## RSVP Flow

Each guest receives a unique public URL:

```text
/invite/{invite_code}
```

Opening the invite page:

- Finds the guest by `invite_code`
- Changes status from `pending` to `viewed`
- Sets `viewed_at`
- Shows guest and event details

Responding:

- Guest can confirm or decline
- If confirming, guest chooses a number from `1` to `max_seats`
- Confirm sets:
  - `status = confirmed`
  - `confirmed_seats = selected value`
  - `responded_at = now()`
- Decline sets:
  - `status = declined`
  - `confirmed_seats = 0`
  - `responded_at = now()`

Once a guest confirms or declines, they cannot change their RSVP from the public page.

Admins can reopen a guest by editing the guest and setting status back to `pending` or `viewed`, which clears `responded_at`.

## RSVP Emails

After a guest confirms or declines, the app sends:

- An owner email to `$guest->event->user->email`
- A guest email to `$guest->email`, only if the guest has an email address

Mail class:

```text
app/Mail/RsvpResponseMail.php
```

Email views:

```text
resources/views/emails/rsvp/owner.blade.php
resources/views/emails/rsvp/guest.blade.php
```

## Guest Management

Authenticated event owners can:

- Create guests
- Edit guests
- Delete guests
- Import guests from CSV
- Export guests and QR codes

Guests are nested under events:

```text
/events/{event}/guests
```

Access checks ensure the event belongs to the authenticated user and the guest belongs to that event.

## CSV Import

Import page:

```text
/events/{event}/guests/import
```

The import view includes a downloadable sample CSV.

Expected headers:

```csv
name,email,mobile,max_seats
```

Sample row:

```csv
Jane Guest,jane@example.com,+96170000000,2
```

Rules:

- `name` is required
- `email` is optional but must be valid if present
- `mobile` is optional
- `max_seats` is optional and defaults to `1`
- `invite_code` is generated automatically
- Imported guests start as `pending`
- Imported guests start with `confirmed_seats = 0`

Capacity behavior:

- If event capacity is `null`, no capacity check is performed
- If capacity is set, current allocated guest seats plus imported seats must be less than or equal to event capacity
- If the import exceeds capacity, no rows are imported

## Guest Export

Export button is available on the guest list page.

The export downloads a ZIP file containing:

```text
guests.csv
qr-codes/{guest-name}-{invite-code}.svg
```

The CSV includes:

```text
Name, Email, Max Seats, Invite Code, RSVP Link, QR Code File
```

This format is designed for printing centers. The CSV contains guest data and references the QR image file path, while the ZIP includes the actual QR SVG files.

## QR Codes

QR codes point to:

```text
route('rsvp.show', ['code' => $guest->invite_code])
```

Reusable component:

```text
resources/views/components/qr-link.blade.php
```

The component:

- Shows a QR preview
- Links to the public RSVP page
- Provides an SVG download button

## Copyable Text

Invite codes use a reusable copy-to-clipboard component:

```text
resources/views/components/copyable-text.blade.php
```

It copies the value and shows a small `Copied` tooltip.

Example:

```blade
<x-copyable-text :value="$guest->invite_code" label="Copy invite code" />
```

## Routes

Main authenticated routes:

```text
GET    /                         dashboard
GET    /events                   events.index
GET    /events/create            events.create
POST   /events/store             events.store
GET    /events/{event}           events.show
GET    /events/{event}/edit      events.edit
PATCH  /events/{event}           events.update
DELETE /events/{event}           events.destroy
```

Guest routes:

```text
GET    /events/{event}/guests                 guests.index
GET    /events/{event}/guests/create          guests.create
POST   /events/{event}/guests                 guests.store
GET    /events/{event}/guests/export          guests.export
GET    /events/{event}/guests/{guest}/edit    guests.edit
PATCH  /events/{event}/guests/{guest}         guests.update
DELETE /events/{event}/guests/{guest}         guests.destroy
GET    /events/{event}/guests/import          guests.import
POST   /events/{event}/guests/import          guests.import.store
GET    /events/{event}/guests/import/sample   guests.import.sample
```

Public RSVP routes:

```text
GET    /invite/{code}   rsvp.show
POST   /invite/{code}   rsvp.respond
```

Authentication and profile routes are provided by Laravel Breeze.

## Important Files

Controllers:

```text
app/Http/Controllers/DashboardController.php
app/Http/Controllers/EventController.php
app/Http/Controllers/GuestController.php
app/Http/Controllers/ImportController.php
app/Http/Controllers/RSVPController.php
```

Models:

```text
app/Models/User.php
app/Models/Event.php
app/Models/Guest.php
app/Models/GuestLog.php
app/Models/Import.php
```

Guest views:

```text
resources/views/guests/index.blade.php
resources/views/guests/create.blade.php
resources/views/guests/edit.blade.php
resources/views/guests/import.blade.php
```

RSVP view:

```text
resources/views/rsvp/show.blade.php
```

Reusable components:

```text
resources/views/components/copyable-text.blade.php
resources/views/components/qr-link.blade.php
```

## Development Commands

Run tests:

```bash
php artisan test
```

Compile Blade views:

```bash
php artisan view:cache
php artisan view:clear
```

List routes:

```bash
php artisan route:list
```

Build assets:

```bash
npm run build
```

Run Vite only:

```bash
npm run dev
```

## Notes

- QR downloads use SVG because SVG remains sharp for envelope printing.
- CSV cannot embed images directly, so the export uses a ZIP with a CSV plus QR image files.
- Public RSVP pages are not authenticated; security depends on unique invite codes.
- Mail delivery depends on `.env` mail configuration.
