<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{   
    public function index(): View
    {
        $events = Auth::user()->events()
            ->withCount([
                'guests',
                'guests as confirmed_guests_count' => fn ($q) => $q->where('status', 'confirmed'),
            ])
            ->latest() 
            ->get();

        return view('events.index', [
            'events' => $events,
        ]);
    }
    
    public function create():View
    {
        return view('events.create');
    }

    public function store(Request $request){

        if (auth()->user()->events->count() >= auth()->user()->max_events) {
            return redirect()->route('dashboard')
                ->with('error', 'You have reached your events limit.');
        }

        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'capacity' => 'nullable|numeric|min:1',
            'event_date' => 'required|date|after_or_equal:today',
            'expires_at' => 'required|date|before_or_equal:event_date',
        ]);        

        $validated['user_id'] = auth()->id();
        Event::create($validated);

        return redirect()->route('events.index')
                     ->with('success', 'Post created successfully!');

    }

    public function show(Event $event): View
    {
        abort_if($event->user_id !== Auth::id(), 403);
        return view('events.show', compact('event'));
    }

    public function edit(Event $event): View
    {
        abort_if($event->user_id !== auth()->id(), 403);
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        abort_if($event->user_id !== auth()->id(), 403);
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'capacity' => 'nullable|numeric|min:1',
            'event_date' => 'required|date|after_or_equal:today', 
            'expires_at' => 'required|date|before_or_equal:event_date',
        ]);

        $event->update($validated);

        return redirect()->route('events.index')
                        ->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event){
        abort_if($event->user_id !== auth()->id(), 403);
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event moved to trash.');
    }
}