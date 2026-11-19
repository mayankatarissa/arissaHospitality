<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Room;
use App\Models\User;
use App\Notifications\BookingUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function request(User $user, Room $room, array $data): Booking
    {
        return DB::transaction(function () use ($user, $room, $data): Booking {
            $lockedRoom = Room::query()->lockForUpdate()->findOrFail($room->id);

            if ($lockedRoom->status !== 'active') {
                throw ValidationException::withMessages([
                    'room' => 'This room is not currently accepting booking requests.',
                ]);
            }

            if ($this->hasApprovedConflict($lockedRoom, $data['check_in_at'], $data['check_out_at'])) {
                throw ValidationException::withMessages([
                    'check_in_at' => 'The room is no longer available for the selected period.',
                ]);
            }

            $booking = $user->bookings()->create([
                'room_id' => $lockedRoom->id,
                'check_in_at' => $data['check_in_at'],
                'check_out_at' => $data['check_out_at'],
                'special_requests' => $data['special_requests'] ?? null,
                'status' => 'pending',
            ]);

            $booking->statusHistories()->create([
                'new_status' => 'pending',
                'note' => 'Booking request submitted.',
            ]);

            User::query()
                ->whereIn('role', ['admin', 'manager'])
                ->where('is_active', true)
                ->get()
                ->each(fn (User $recipient) => $recipient->notify(
                    new BookingUpdated($booking, 'A new room booking request needs review.'),
                ));

            return $booking;
        });
    }

    public function approve(Booking $booking, User $actor): Booking
    {
        return DB::transaction(function () use ($booking, $actor): Booking {
            $room = Room::query()->lockForUpdate()->findOrFail($booking->room_id);
            $lockedBooking = Booking::query()->lockForUpdate()->findOrFail($booking->id);

            if ($lockedBooking->status !== 'pending') {
                throw ValidationException::withMessages([
                    'booking' => 'Only pending booking requests can be approved.',
                ]);
            }

            if ($room->status !== 'active' || $this->hasApprovedConflict(
                $room,
                $lockedBooking->check_in_at,
                $lockedBooking->check_out_at,
                $lockedBooking->id,
            )) {
                throw ValidationException::withMessages([
                    'booking' => 'This room is no longer available for the requested period.',
                ]);
            }

            $oldStatus = $lockedBooking->status;
            $lockedBooking->update(['status' => 'approved']);
            $this->recordStatus($lockedBooking, $actor, $oldStatus);
            $lockedBooking->user->notify(new BookingUpdated($lockedBooking, 'Your room booking has been approved.'));

            return $lockedBooking;
        });
    }

    public function reject(Booking $booking, User $actor, ?string $reason = null): Booking
    {
        return DB::transaction(function () use ($booking, $actor, $reason): Booking {
            Room::query()->lockForUpdate()->findOrFail($booking->room_id);
            $lockedBooking = Booking::query()->lockForUpdate()->findOrFail($booking->id);

            if ($lockedBooking->status !== 'pending') {
                throw ValidationException::withMessages([
                    'booking' => 'Only pending booking requests can be rejected.',
                ]);
            }

            $oldStatus = $lockedBooking->status;
            $lockedBooking->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);
            $this->recordStatus($lockedBooking, $actor, $oldStatus, $reason);
            $lockedBooking->user->notify(new BookingUpdated($lockedBooking, 'Your room booking request was not approved.'));

            return $lockedBooking;
        });
    }

    public function cancel(Booking $booking, User $actor): Booking
    {
        return DB::transaction(function () use ($booking, $actor): Booking {
            Room::query()->lockForUpdate()->findOrFail($booking->room_id);
            $lockedBooking = Booking::query()->lockForUpdate()->findOrFail($booking->id);

            if ($lockedBooking->user_id !== $actor->id
                || ! in_array($lockedBooking->status, ['pending', 'approved'], true)
                || $lockedBooking->check_in_at->isPast()) {
                abort(403);
            }

            $oldStatus = $lockedBooking->status;
            $lockedBooking->update(['status' => 'cancelled']);
            $this->recordStatus($lockedBooking, $actor, $oldStatus);
            $actor->notify(new BookingUpdated($lockedBooking, 'Your booking has been cancelled.'));

            User::query()
                ->whereIn('role', ['admin', 'manager'])
                ->where('is_active', true)
                ->get()
                ->each(fn (User $recipient) => $recipient->notify(
                    new BookingUpdated($lockedBooking, 'A room booking has been cancelled.'),
                ));

            return $lockedBooking;
        });
    }

    private function hasApprovedConflict(Room $room, mixed $checkIn, mixed $checkOut, ?int $exceptBookingId = null): bool
    {
        return Booking::query()
            ->where('room_id', $room->id)
            ->where('status', 'approved')
            ->when($exceptBookingId, fn ($query) => $query->whereKeyNot($exceptBookingId))
            ->where('check_in_at', '<', $checkOut)
            ->where('check_out_at', '>', $checkIn)
            ->exists();
    }

    private function recordStatus(Booking $booking, User $actor, ?string $oldStatus, ?string $note = null): void
    {
        BookingStatusHistory::query()->create([
            'booking_id' => $booking->id,
            'changed_by' => $actor->id,
            'old_status' => $oldStatus,
            'new_status' => $booking->status,
            'note' => $note,
        ]);
    }
}
