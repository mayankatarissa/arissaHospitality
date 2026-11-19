<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function create(Request $request): View
    {
        return view('public.review-form', [
            'hasReview' => $request->user()->reviews()->exists(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['user_id' => $request->user()->id]);
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:3000'],
            'user_id' => [Rule::unique('reviews', 'user_id')],
        ]);

        $request->user()->reviews()->create([
            'rating' => $data['rating'],
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => 'pending',
        ]);

        return redirect()->route('reviews.index')->with('status', 'Thank you. Your review is awaiting moderation.');
    }
}
