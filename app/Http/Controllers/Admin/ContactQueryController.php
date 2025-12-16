<?php

namespace App\Http\Controllers\Admin;

use App\Models\ContactSubmission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactQueryController extends Controller
{
    public function index()
    {
        $queries = ContactSubmission::orderBy('created_at', 'desc')->get();
        return inertia('Admin/ContactQueries', ['queries' => $queries]);
    }

    public function markAsRead(Request $request, ContactSubmission $query)
    {
        $query->update(['read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Query marked as read',
        ]);
    }

    public function markAsReplied(Request $request, ContactSubmission $query)
    {
        $query->update(['replied' => true, 'read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Query marked as replied',
        ]);
    }

    public function destroy(ContactSubmission $query)
    {
        $query->delete();

        return response()->json([
            'success' => true,
            'message' => 'Query deleted successfully',
        ]);
    }
}
