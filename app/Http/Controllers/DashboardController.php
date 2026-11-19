<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\Review;
use App\Models\StaffDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        return match ($request->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'manager' => redirect()->route('manager.dashboard'),
            default => redirect()->route('user.dashboard'),
        };
    }

    public function admin(): View
    {
        return view('dashboards.admin', $this->dashboardData(true));
    }

    public function manager(): View
    {
        return view('dashboards.manager', $this->dashboardData(false));
    }

    public function user(): View
    {
        $now = now();

        return view('dashboards.user', [
            'totalRooms' => Room::query()->where('status', 'active')->count(),
            'approvedBookings' => Booking::query()->where('status', 'approved')->count(),
            'pendingBookings' => Booking::query()->where('status', 'pending')->count(),
            'availableRooms' => Room::query()
                ->where('status', 'active')
                ->whereDoesntHave('bookings', function ($query) use ($now): void {
                    $query->where('status', 'approved')
                        ->where('check_in_at', '<=', $now)
                        ->where('check_out_at', '>', $now);
                })
                ->count(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function dashboardData(bool $admin): array
    {
        $now = now();
        $activity = Booking::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::today()->subDays(6))
            ->groupBy('day')
            ->pluck('total', 'day');
        $dates = collect(range(6, 0))->map(fn (int $days) => Carbon::today()->subDays($days));
        $data = [
            'totalRooms' => Room::query()->where('status', 'active')->count(),
            'approvedBookings' => Booking::query()->where('status', 'approved')->count(),
            'pendingBookings' => Booking::query()->where('status', 'pending')->count(),
            'availableRooms' => Room::query()
                ->where('status', 'active')
                ->whereDoesntHave('bookings', function ($query) use ($now): void {
                    $query->where('status', 'approved')
                        ->where('check_in_at', '<=', $now)
                        ->where('check_out_at', '>', $now);
                })
                ->count(),
            'recentBookings' => Booking::query()->with(['user', 'room'])->latest()->limit(8)->get(),
            'activityLabels' => $dates->map(fn (Carbon $date) => $date->format('M j'))->values(),
            'activityValues' => $dates->map(fn (Carbon $date) => (int) $activity->get($date->toDateString(), 0))->values(),
            'isAdmin' => $admin,
        ];

        if ($admin) {
            $data += [
                'totalUsers' => User::query()->where('role', 'user')->count(),
                'totalManagers' => User::query()->where('role', 'manager')->count(),
                'totalStaff' => StaffDetail::query()->where('status', 'active')->count(),
                'totalReviews' => Review::query()->count(),
                'totalFacilities' => Facility::query()->where('is_active', true)->count(),
            ];
        }

        return $data;
    }
}
