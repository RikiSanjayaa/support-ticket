<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $data = [];

        switch ($user->role) {
            case 'user':
                $data['tickets'] = Ticket::where('created_by', $user->id)
                    ->latest()
                    ->take(5)
                    ->get();
                break;

            case 'agent':
                $data['assignedTickets'] = Ticket::where('assigned_to', $user->id)->count();
                $data['openTickets'] = Ticket::where('status', 'open')->count();
                $data['inProgressTickets'] = Ticket::where('status', 'in_progress')->count();
                $data['resolvedToday'] = Ticket::where('resolved_by', $user->id)
                    ->whereDate('resolved_at', today())
                    ->count();
                $data['recentTickets'] = Ticket::where(function ($query) use ($user) {
                    $query->where('assigned_to', $user->id)
                        ->orWhereNull('assigned_to');
                })
                    ->latest()
                    ->take(5)
                    ->get();
                break;

            case 'admin':
                $data['totalTickets'] = Ticket::count();
                $data['openTickets'] = Ticket::where('status', 'open')->count();
                $data['agentsCount'] = User::where('role', 'agent')->count();
                $data['responseRate'] = $this->calculateResponseRate();

                // Get agent performance data
                $data['agentPerformance'] = User::where('role', 'agent')
                    ->withCount(['assignedTickets', 'resolvedTickets'])
                    ->get()
                    ->map(function ($agent) {
                        $total = $agent->assigned_tickets_count;
                        $resolved = $agent->resolved_tickets_count;

                        return (object)[
                            'name' => $agent->name,
                            'assigned_count' => $total,
                            'resolved_count' => $resolved,
                            'resolution_rate' => $total > 0 ? round(($resolved / $total) * 100) : 0,
                            'average_response_time' => $this->calculateAverageResponseTime($agent->id)
                        ];
                    });
                break;
        }

        return view('dashboard', $data);
    }

    private function calculateResponseRate()
    {
        $totalTickets = Ticket::where('status', '!=', 'open')->count();
        $resolvedTickets = Ticket::where('status', 'closed')->count();

        if ($totalTickets === 0) {
            return '0%';
        }

        return round(($resolvedTickets / $totalTickets) * 100) . '%';
    }

    private function calculateAverageResponseTime($agentId)
    {
        $resolvedTickets = Ticket::where('resolved_by', $agentId)
            ->whereNotNull('resolved_at')
            ->get();

        if ($resolvedTickets->isEmpty()) {
            return null;
        }

        $totalTime = 0;
        foreach ($resolvedTickets as $ticket) {
            $totalTime += $ticket->resolved_at->diffInHours($ticket->created_at);
        }

        $averageHours = round($totalTime / $resolvedTickets->count());
        return $averageHours > 24
            ? round($averageHours / 24) . ' days'
            : $averageHours . ' hours';
    }
}
