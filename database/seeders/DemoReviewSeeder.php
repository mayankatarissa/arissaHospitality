<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoReviewSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            [
                'email' => 'demo.reviewer.one@example.com',
                'name' => 'Sample Guest One (Demo)',
                'rating' => 5,
                'title' => '[DEMO] A restful, welcoming stay',
                'description' => 'Demo review only: this sample describes a calm and comfortable stay. It is not feedback from an actual guest.',
            ],
            [
                'email' => 'demo.reviewer.two@example.com',
                'name' => 'Sample Guest Two (Demo)',
                'rating' => 4,
                'title' => '[DEMO] Attentive service',
                'description' => 'Demo review only: this sample describes attentive service and an enjoyable visit. It is not feedback from an actual guest.',
            ],
            [
                'email' => 'demo.reviewer.three@example.com',
                'name' => 'Sample Guest Three (Demo)',
                'rating' => 5,
                'title' => '[DEMO] A memorable getaway',
                'description' => 'Demo review only: this sample describes a memorable weekend away. It is not feedback from an actual guest.',
            ],
        ] as $reviewData) {
            $user = User::query()->firstOrCreate(
                ['email' => $reviewData['email']],
                [
                    'name' => $reviewData['name'],
                    'password' => Str::random(48),
                    'is_active' => false,
                ],
            );

            Review::query()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'rating' => $reviewData['rating'],
                    'title' => $reviewData['title'],
                    'description' => $reviewData['description'],
                    'status' => 'approved',
                ],
            );
        }
    }
}
