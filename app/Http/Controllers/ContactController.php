<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'project_type' => ['nullable', 'string', 'max:60'],
            'budget' => ['nullable', 'string', 'max:60'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        // Store the inquiry so it appears in the admin panel Messages section.
        \App\Models\Message::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'project_type' => $validated['project_type'] ?? null,
            'budget' => $validated['budget'] ?? null,
            'message' => $validated['message'],
            'status' => 'new',
        ]);

        // Notify the studio. Configure MAIL_* in .env to enable delivery.
        try {
            Mail::raw(
                "New inquiry from {$validated['name']} <{$validated['email']}>\n"
                ."Project type: ".($validated['project_type'] ?? '—')."\n"
                ."Budget: ".($validated['budget'] ?? '—')."\n\n"
                .$validated['message'],
                function ($message) use ($validated) {
                    $message->to(config('mail.from.address'))
                        ->subject('New project inquiry — '.$validated['name']);
                }
            );
        } catch (\Throwable $e) {
            Log::warning('Contact inquiry could not be mailed: '.$e->getMessage());
        }

        Log::info('Contact inquiry received', $validated);

        return redirect()
            ->route('contact')
            ->with('success', 'Thank you — your inquiry has been received. We will reply within two working days.');
    }
}