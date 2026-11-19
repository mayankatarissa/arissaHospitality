<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingUpdated extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Booking $booking,
        private readonly string $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $url = $notifiable instanceof User && in_array($notifiable->role, ['admin', 'manager'], true)
            ? route('management.bookings.show', $this->booking)
            : route('bookings.show', $this->booking);

        return [
            'message' => $this->message,
            'booking_id' => $this->booking->id,
            'url' => $url,
        ];
    }
}
