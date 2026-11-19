<?php

namespace App\Http\Controllers;

use App\Models\EventEnquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EventEnquiryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'event_type' => ['nullable', 'string', 'max:100'],
            'event_date' => ['nullable', 'date', 'after_or_equal:today'],
            'guest_count' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        EventEnquiry::query()->create($data);

        return back()->with('status', 'Thank you. Our events team will be in touch.');
    }
}
