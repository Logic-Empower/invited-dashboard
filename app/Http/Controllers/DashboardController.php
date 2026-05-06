<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View 
    {
        $events = Auth::user()->events()
            ->withCount([
                'guests',
                'guests as confirmed_guests_count' => fn ($q) => $q->where('status', 'confirmed'),
            ])
            ->latest() // Orders by created_at DESC
            ->limit(4)
            ->get();

        return view('dashboard', [
            'events' => $events,
        ]);
    }
}
