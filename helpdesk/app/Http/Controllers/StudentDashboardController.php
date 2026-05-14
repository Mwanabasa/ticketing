<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $openCount = Ticket::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['open', 'pending'])
            ->count();

        $recentTickets = Ticket::query()
            ->where('user_id', $user->id)
            ->with('category')
            ->latest()
            ->limit(5)
            ->get();

        return view('student.dashboard', [
            'openCount' => $openCount,
            'recentTickets' => $recentTickets,
        ]);
    }
}
