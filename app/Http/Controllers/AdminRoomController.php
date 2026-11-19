<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminRoomController extends Controller
{
    public function index(): View
    {
        return view('admin.rooms.index', [
            'rooms' => Room::query()->with('category')->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.rooms.form', [
            'room' => new Room,
            'categories' => RoomCategory::query()->where('is_active', true)->orderBy('name')->get(),
            'facilities' => Facility::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        unset($data['image']);
        $data['image_path'] = $request->hasFile('image')
            ? $request->file('image')->store('rooms', 'public')
            : null;

        $facilityIds = $data['facilities'] ?? [];
        unset($data['facilities']);
        $room = Room::query()->create($data);
        $room->facilities()->sync($facilityIds);

        return redirect()->route('admin.rooms.index')->with('status', 'Room created.');
    }

    public function edit(Room $room): View
    {
        return view('admin.rooms.form', [
            'room' => $room,
            'categories' => RoomCategory::query()
                ->where('is_active', true)
                ->orWhereKey($room->room_category_id)
                ->orderBy('name')
                ->get(),
            'facilities' => Facility::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $data = $this->validatedData($request, $room);
        unset($data['image']);
        $facilityIds = $data['facilities'] ?? [];
        unset($data['facilities']);
        $oldImage = $room->image_path;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('rooms', 'public');
        }

        $room->update($data);
        $room->facilities()->sync($facilityIds);

        if ($request->hasFile('image') && $oldImage) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('admin.rooms.index')->with('status', 'Room updated.');
    }

    public function deactivate(Room $room): RedirectResponse
    {
        $room->update(['status' => 'inactive']);

        return back()->with('status', 'Room deactivated. Its booking history has been retained.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, ?Room $room = null): array
    {
        return $request->validate([
            'room_category_id' => ['required', 'integer', 'exists:room_categories,id'],
            'room_number' => ['required', 'string', 'max:30', Rule::unique('rooms', 'room_number')->ignore($room?->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price_per_night' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'max_occupancy' => ['required', 'integer', 'min:1', 'max:100'],
            'status' => ['required', Rule::in(['active', 'maintenance', 'inactive'])],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'facilities' => ['sometimes', 'array'],
            'facilities.*' => ['integer', 'distinct', 'exists:facilities,id'],
        ]);
    }
}
