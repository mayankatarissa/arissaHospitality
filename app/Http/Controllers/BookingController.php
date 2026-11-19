<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Room $room, BookingService $bookings): RedirectResponse
    {
        $data = $request->validate([
            'check_in_at' => ['required', 'date', 'after:now'],
            'check_out_at' => ['required', 'date', 'after:check_in_at'],
            'special_requests' => ['nullable', 'string', 'max:2000'],
        ]);

        $booking = $bookings->request($request->user(), $room, $data);

        return redirect()->route('bookings.show', $booking)
            ->with('status', 'Your booking request was submitted for review.');
    }

    public function index(Request $request): View
    {
        return view('bookings.index', [
            'bookings' => $request->user()->bookings()
                ->with('room.category')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        return view('bookings.show', [
            'booking' => $booking->load(['room.category', 'statusHistories.changedBy']),
        ]);
    }

    public function cancel(Booking $booking, BookingService $bookings): RedirectResponse
    {
        $this->authorize('cancel', $booking);
        $bookings->cancel($booking, request()->user());

        return back()->with('status', 'Your booking was cancelled.');
    }
}
