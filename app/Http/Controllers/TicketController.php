<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ticket::query()
            ->with(['creator', 'assignedAgent']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        if ($request->filled('assigned_to')) {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        if ($request->has('partial')) {
            return view('tickets._table', compact('tickets'));
        }

        $users = User::all();
        $agents = User::where('role', 'agent')->get();

        return view('tickets.index', compact('tickets', 'users', 'agents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tickets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'status' => 'nullable|in:open,in_progress,closed'
        ]);

        // Set default status for dashboard submissions
        if (!isset($validated['status'])) {
            $validated['status'] = 'open';
        }

        // Add the user who created the ticket
        $validated['created_by'] = \Illuminate\Support\Facades\Auth::user()->id;

        Ticket::create($validated);

        return redirect()->route('dashboard')
            ->with('status', 'ticket-created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        if (!(\Illuminate\Support\Facades\Auth::user()->role === 'admin' ||
            \Illuminate\Support\Facades\Auth::user()->role === 'agent' ||
            \Illuminate\Support\Facades\Auth::user()->id === $ticket->created_by)) {
            abort(403);
        }

        $agents = User::where('role', 'agent')->get();
        return view('tickets.edit', compact('ticket', 'agents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        if (!(\Illuminate\Support\Facades\Auth::user()->role === 'admin' ||
            \Illuminate\Support\Facades\Auth::user()->role === 'agent' ||
            \Illuminate\Support\Facades\Auth::user()->id === $ticket->created_by)) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|max:255|string',
            'description' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        // Only allow admin to change assigned_to
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            unset($validated['assigned_to']);
        }

        $ticket->update($validated);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
    }

    /**
     * Assign the ticket to the authenticated agent.
     */
    public function assign(Ticket $ticket)
    {
        // Check if user is an agent
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'agent' && \Illuminate\Support\Facades\Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // If ticket is already assigned to this agent, unassign it
        if ($ticket->assigned_to === \Illuminate\Support\Facades\Auth::user()->id) {
            $ticket->update([
                'assigned_to' => null,
                'status' => 'open'
            ]);
            $message = 'Ticket unassigned successfully';
        } else {
            // Assign ticket to this agent
            $ticket->update([
                'assigned_to' => \Illuminate\Support\Facades\Auth::user()->id,
                'status' => 'in_progress'
            ]);
            $message = 'Ticket assigned successfully';
        }

        return redirect()->back()->with('success', 'Ticket assigned successfully');
    }

    /**
     * Mark the ticket as resolved.
     */
    public function resolve(Ticket $ticket)
    {
        // Check if user is authorized to resolve the ticket
        $role = \Illuminate\Support\Facades\Auth::user()->role;
        if (!($role === 'agent' || $role === 'admin' || Auth::id() === $ticket->created_by)) {
            echo $ticket->created_by;
            echo \Illuminate\Support\Facades\Auth::user()->id;
            abort(403, 'Unauthorized action.');
        }

        // Check if the ticket is assigned to the current agent or the creator itself
        if (!($ticket->assigned_to === Auth::id() || $ticket->created_by === Auth::id())) {
            return redirect()->back()->with('error', 'You can only resolve tickets assigned to you or tickets you created.');
        }

        // Update ticket status and resolution details
        $ticket->update([
            'status' => 'closed',
            'resolved_by' => \Illuminate\Support\Facades\Auth::user()->id,
            'resolved_at' => now()
        ]);

        return redirect()->back()->with('success', 'Ticket marked as resolved');
    }

    public function reopen(Ticket $ticket)
    {
        if (\Illuminate\Support\Facades\Auth::user()->role !== 'agent' && \Illuminate\Support\Facades\Auth::user()->id !== $ticket->created_by) {
            abort(403);
        }

        $ticket->update([
            'status' => 'open',
            'resolved_at' => null,
            'resolved_by' => null
        ]);

        return back()->with('success', 'Ticket reopened successfully');
    }
}
