<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Ticket;
use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReplyController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'content' => ['required', 'string']
        ]);

        $ticket->replies()->create([
            'content' => $validated['content'],
            'user_id' => \Illuminate\Support\Facades\Auth::user()->id
        ]);

        return back()->with('status', 'reply-created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reply $reply)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reply $reply)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reply $reply)
    {
        if (\Illuminate\Support\Facades\Auth::user()->id !== $reply->user_id) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => ['required', 'string']
        ]);

        $reply->update($validated);

        return back()->with('success', 'Reply updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('delete', $reply);

        $reply->delete();

        return back()->with('status', 'reply-deleted');
    }
}
