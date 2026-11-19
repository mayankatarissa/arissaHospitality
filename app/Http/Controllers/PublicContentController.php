<?php

namespace App\Http\Controllers;

use App\Models\EventVenue;
use App\Models\Facility;
use App\Models\HotelSetting;
use App\Models\RestaurantMenuItem;
use App\Models\Review;
use Illuminate\View\View;

class PublicContentController extends Controller
{
    public function facilities(): View
    {
        return view('public.facilities', [
            'facilities' => Facility::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function foodMenu(): View
    {
        return $this->menu('food');
    }

    public function barMenu(): View
    {
        return $this->menu('bar');
    }

    public function about(): View
    {
        return view('public.content', [
            'content' => $this->setting('about'),
            'eyebrow' => 'Our story',
        ]);
    }

    public function resort(): View
    {
        return view('public.content', [
            'content' => $this->setting('resort'),
            'eyebrow' => 'The resort',
        ]);
    }

    public function events(): View
    {
        return view('public.events', [
            'venues' => EventVenue::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function reviews(): View
    {
        return view('public.reviews', [
            'reviews' => Review::query()->with('user')->where('status', 'approved')->latest()->paginate(12),
        ]);
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    private function menu(string $type): View
    {
        return view('public.menu', [
            'type' => $type,
            'items' => RestaurantMenuItem::query()
                ->where('menu_type', $type)
                ->where('is_available', true)
                ->orderBy('category')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function setting(string $key): array
    {
        return HotelSetting::query()->where('key', $key)->first()?->value ?? [];
    }
}
