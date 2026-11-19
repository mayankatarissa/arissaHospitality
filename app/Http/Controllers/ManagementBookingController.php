<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManagementBookingController extends Controller
{
    public function index(Request $request): View
    {
        return view('management.bookings.index', [
            'bookings' => Booking::query()
                ->with(['user', 'room.category'])
                ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        ]);
    }

    public function show(Booking $booking): View
    {
        return view('management.bookings.show', [
            'booking' => $booking->load(['user', 'room.category', 'statusHistories.changedBy']),
        ]);
    }

    public function approve(Booking $booking, BookingService $bookings): RedirectResponse
    {
        $bookings->approve($booking, request()->user());

        return back()->with('status', 'Booking approved and the guest has been notified.');
    }

    public function reject(Request $request, Booking $booking, BookingService $bookings): RedirectResponse
    {
        $data = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $bookings->reject($booking, $request->user(), $data['rejection_reason'] ?? null);

        return back()->with('status', 'Booking rejected and the guest has been notified.');
    }
}
