<?php

namespace App\Http\Controllers;

use App\Models\EventVenue;
use App\Models\Facility;
use App\Models\RestaurantMenuItem;
use App\Models\RoomCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\View\View;

class AdminCatalogController extends Controller
{
    private const TYPES = ['categories', 'facilities', 'food', 'bar', 'venues'];

    public function index(string $type): View
    {
        $this->ensureType($type);
        $model = $this->modelFor($type);
        $items = $model::query()->latest()->paginate(20);

        return view('admin.catalog.index', [
            'type' => $type,
            'title' => $this->titleFor($type),
            'items' => $items,
        ]);
    }

    public function create(string $type): View
    {
        $this->ensureType($type);
        $model = $this->modelFor($type);

        return $this->form($type, new $model);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $this->ensureType($type);
        $data = $this->validatedData($request, $type);
        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($type === 'categories') {
            $data['slug'] = Str::slug($data['name']);
        } elseif (in_array($type, ['food', 'bar'], true)) {
            $data['menu_type'] = $type;
        }

        if ($image) {
            $data['image_path'] = $image->store($this->imageDirectory($type), 'public');
        }

        $model = $this->modelFor($type);
        $model::query()->create($data);

        return redirect()->route('admin.catalog.index', $type)->with('status', $this->titleFor($type).' entry created.');
    }

    public function edit(string $type, int $id): View
    {
        $this->ensureType($type);

        return $this->form($type, $this->modelFor($type)::query()->findOrFail($id));
    }

    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        $this->ensureType($type);
        $record = $this->modelFor($type)::query()->findOrFail($id);
        $data = $this->validatedData($request, $type, $record);
        $image = $data['image'] ?? null;
        unset($data['image']);

        if ($type === 'categories') {
            $data['slug'] = Str::slug($data['name']);
        }
        if ($image) {
            $data['image_path'] = $image->store($this->imageDirectory($type), 'public');
        }

        $oldImage = $record->image_path;
        $record->update($data);

        if ($image && $oldImage) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($oldImage);
        }

        return redirect()->route('admin.catalog.index', $type)->with('status', $this->titleFor($type).' entry updated.');
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        $this->ensureType($type);
        $record = $this->modelFor($type)::query()->findOrFail($id);

        if ($record instanceof RoomCategory && $record->rooms()->exists()) {
            return back()->withErrors(['record' => 'This category still has rooms. Deactivate it instead of deleting it.']);
        }

        if ($record->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($record->image_path);
        }

        $record->delete();

        return back()->with('status', $this->titleFor($type).' entry deleted.');
    }

    private function form(string $type, Model $record): View
    {
        return view('admin.catalog.form', [
            'type' => $type,
            'title' => $this->titleFor($type),
            'record' => $record,
            'fields' => $this->fieldsFor($type),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedData(Request $request, string $type, ?Model $record = null): array
    {
        $imageRules = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];

        return match ($type) {
            'categories' => $request->validate([
                'name' => ['required', 'string', 'max:120', $this->uniqueRule('room_categories', 'name', $record)],
                'description' => ['nullable', 'string', 'max:2000'],
                'default_occupancy' => ['nullable', 'integer', 'min:1', 'max:100'],
                'is_active' => ['required', 'boolean'],
            ]),
            'facilities' => $request->validate([
                'name' => ['required', 'string', 'max:150', $this->uniqueRule('facilities', 'name', $record)],
                'description' => ['nullable', 'string', 'max:3000'],
                'operating_hours' => ['nullable', 'string', 'max:255'],
                'is_active' => ['required', 'boolean'],
                'image' => $imageRules,
            ]),
            'food', 'bar' => $request->validate([
                'name' => ['required', 'string', 'max:150'],
                'description' => ['nullable', 'string', 'max:3000'],
                'category' => ['required', 'string', 'max:100'],
                'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
                'is_available' => ['required', 'boolean'],
                'image' => $imageRules,
            ]),
            'venues' => $request->validate([
                'name' => ['required', 'string', 'max:150', $this->uniqueRule('event_venues', 'name', $record)],
                'venue_type' => ['required', 'string', 'max:30'],
                'description' => ['required', 'string', 'max:3000'],
                'capacity' => ['nullable', 'integer', 'min:1', 'max:10000'],
                'is_active' => ['required', 'boolean'],
                'image' => $imageRules,
            ]),
        };
    }

    /**
     * @return array<string, array{label: string, type: string, required?: bool}>
     */
    private function fieldsFor(string $type): array
    {
        return match ($type) {
            'categories' => [
                'name' => ['label' => 'Category name', 'type' => 'text', 'required' => true],
                'description' => ['label' => 'Description', 'type' => 'textarea'],
                'default_occupancy' => ['label' => 'Default occupancy', 'type' => 'number'],
                'is_active' => ['label' => 'Active', 'type' => 'checkbox'],
            ],
            'facilities' => [
                'name' => ['label' => 'Facility name', 'type' => 'text', 'required' => true],
                'description' => ['label' => 'Description', 'type' => 'textarea'],
                'operating_hours' => ['label' => 'Operating hours', 'type' => 'text'],
                'is_active' => ['label' => 'Active', 'type' => 'checkbox'],
                'image' => ['label' => 'Image (JPG, PNG or WebP; up to 5 MB)', 'type' => 'file'],
            ],
            'food', 'bar' => [
                'name' => ['label' => 'Menu item', 'type' => 'text', 'required' => true],
                'description' => ['label' => 'Description', 'type' => 'textarea'],
                'category' => ['label' => 'Category', 'type' => 'text', 'required' => true],
                'price' => ['label' => 'Price', 'type' => 'number', 'required' => true],
                'is_available' => ['label' => 'Available', 'type' => 'checkbox'],
                'image' => ['label' => 'Image (JPG, PNG or WebP; up to 5 MB)', 'type' => 'file'],
            ],
            'venues' => [
                'name' => ['label' => 'Venue name', 'type' => 'text', 'required' => true],
                'venue_type' => ['label' => 'Venue type', 'type' => 'text', 'required' => true],
                'description' => ['label' => 'Description', 'type' => 'textarea', 'required' => true],
                'capacity' => ['label' => 'Capacity', 'type' => 'number'],
                'is_active' => ['label' => 'Active', 'type' => 'checkbox'],
                'image' => ['label' => 'Image (JPG, PNG or WebP; up to 5 MB)', 'type' => 'file'],
            ],
        };
    }

    /**
     * @return class-string<Model>
     */
    private function modelFor(string $type): string
    {
        return match ($type) {
            'categories' => RoomCategory::class,
            'facilities' => Facility::class,
            'food', 'bar' => RestaurantMenuItem::class,
            'venues' => EventVenue::class,
        };
    }

    private function titleFor(string $type): string
    {
        return match ($type) {
            'categories' => 'Room categories',
            'facilities' => 'Facilities',
            'food' => 'Food menu',
            'bar' => 'Bar menu',
            'venues' => 'Event venues',
        };
    }

    private function imageDirectory(string $type): string
    {
        return match ($type) {
            'facilities' => 'facilities',
            'venues' => 'event-venues',
            default => 'menu',
        };
    }

    private function ensureType(string $type): void
    {
        abort_unless(in_array($type, self::TYPES, true), 404);
    }

    private function uniqueRule(string $table, string $column, ?Model $record): Unique
    {
        $rule = Rule::unique($table, $column);

        return $record ? $rule->ignore($record->getKey()) : $rule;
    }
}
