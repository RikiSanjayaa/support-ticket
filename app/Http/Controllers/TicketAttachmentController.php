<?php

namespace App\Http\Controllers;

use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketAttachmentController extends Controller
{
    public function destroy(TicketAttachment $attachment)
    {
        // Check if user is authorized to delete the attachment
        if (
            \Illuminate\Support\Facades\Auth::user()->id !== $attachment->ticket->created_by &&
            !in_array(\Illuminate\Support\Facades\Auth::user()->role, ['admin', 'agent'])
        ) {
            abort(403);
        }

        // Delete the file
        Storage::delete($attachment->filename);

        // Delete the record
        $attachment->delete();

        return response()->json(['message' => 'Attachment deleted successfully']);
    }
}
