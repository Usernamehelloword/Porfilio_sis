<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $query = Message::query()->latest();

        if (in_array($request->query('status'), ['new', 'read', 'replied', 'archived'], true)) {
            $query->where('status', $request->query('status'));
        }

        if ($q = trim((string) $request->query('q'))) {
            $query->where(fn ($w) => $w->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('message', 'like', "%{$q}%"));
        }

        return view('admin.messages.index', [
            'messages' => $query->paginate(20)->withQueryString(),
            'counts' => [
                'all' => Message::count(),
                'new' => Message::where('status', 'new')->count(),
                'read' => Message::where('status', 'read')->count(),
                'replied' => Message::where('status', 'replied')->count(),
                'archived' => Message::where('status', 'archived')->count(),
            ],
        ]);
    }

    public function show(Message $message): View
    {
        if ($message->status === 'new') {
            $message->update(['status' => 'read']);
        }

        return view('admin.messages.show', ['message' => $message]);
    }

    public function markRead(Message $message): RedirectResponse
    {
        $message->update(['status' => 'read']);

        return back()->with('success', 'Marked as read.');
    }

    public function markReplied(Message $message): RedirectResponse
    {
        $message->update(['status' => 'replied']);

        return back()->with('success', 'Marked as replied.');
    }

    public function archive(Message $message): RedirectResponse
    {
        $message->update(['status' => 'archived']);

        return redirect()->route('admin.messages.index')->with('success', 'Message archived.');
    }

    public function destroy(Message $message): RedirectResponse
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Message deleted.');
    }
}
