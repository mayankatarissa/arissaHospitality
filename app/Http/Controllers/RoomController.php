<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $validator = Validator::make($request->query(), [
            'check_in_date' => ['nullable', 'date', 'after_or_equal:today'],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_date' => ['nullable', 'date', 'after_or_equal:check_in_date'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'min_budget' => ['nullable', 'numeric', 'min:0'],
            'max_budget' => ['nullable', 'numeric', 'min:0'],
        ]);

        $validator->after(function ($validator) use ($request): void {
            $periodFields = ['check_in_date', 'check_in_time', 'check_out_date', 'check_out_time'];
            $hasPeriodField = collect($periodFields)->contains(fn (string $field) => $request->filled($field));

            if ($hasPeriodField && collect($periodFields)->contains(fn (string $field) => ! $request->filled($field))) {
                $validator->errors()->add('check_in_date', 'Enter both check-in and check-out dates and times.');
            }

            if (collect($periodFields)->every(fn (string $field) => $request->filled($field))) {
                $checkIn = Carbon::parse($request->input('check_in_date').' '.$request->input('check_in_time'));
                $checkOut = Carbon::parse($request->input('check_out_date').' '.$request->input('check_out_time'));

                if ($checkIn->isPast()) {
                    $validator->errors()->add('check_in_date', 'Check-in must not be in the past.');
                }

                if (! $checkOut->greaterThan($checkIn)) {
                    $validator->errors()->add('check_out_date', 'Check-out must be later than check-in.');
                }
            }

            if ($request->filled('min_budget') && $request->filled('max_budget')
                && (float) $request->input('max_budget') < (float) $request->input('min_budget')) {
                $validator->errors()->add('max_budget', 'Maximum budget must be at least the minimum budget.');
            }
        });

        $filters = $validator->validate();
        $rooms = Room::query()
            ->with(['category', 'facilities'])
            ->where('status', 'active')
            ->when(isset($filters['min_budget']), fn ($query) => $query->where('price_per_night', '>=', $filters['min_budget']))
            ->when(isset($filters['max_budget']), fn ($query) => $query->where('price_per_night', '<=', $filters['max_budget']));

        $hasDateSearch = isset($filters['check_in_date'], $filters['check_in_time'], $filters['check_out_date'], $filters['check_out_time']);

        if ($hasDateSearch) {
            $checkIn = Carbon::parse($filters['check_in_date'].' '.$filters['check_in_time']);
            $checkOut = Carbon::parse($filters['check_out_date'].' '.$filters['check_out_time']);

            $rooms->whereDoesntHave('bookings', function ($query) use ($checkIn, $checkOut): void {
                $query->where('status', 'approved')
                    ->where('check_in_at', '<', $checkOut)
                    ->where('check_out_at', '>', $checkIn);
            });
        }

        return view('rooms.index', [
            'rooms' => $rooms->orderBy('price_per_night')->paginate(9)->withQueryString(),
            'filters' => $filters,
            'hasDateSearch' => $hasDateSearch,
        ]);
    }

    public function show(Room $room): View
    {
        abort_unless($room->status === 'active', 404);

        return view('rooms.show', [
            'room' => $room->load(['category', 'facilities']),
        ]);
    }
}
