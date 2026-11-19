<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'featuredRooms' => Room::query()
                ->with('category')
                ->where('status', 'active')
                ->latest()
                ->limit(3)
                ->get(),
        ]);
    }
}
