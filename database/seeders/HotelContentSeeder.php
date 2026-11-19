<?php

namespace Database\Seeders;

use App\Models\EventVenue;
use App\Models\HotelSetting;
use Illuminate\Database\Seeder;

class HotelContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'about' => [
                'title' => 'A gracious welcome, every time',
                'body' => 'Arissa Hospitality brings thoughtful service, inviting spaces and a calm sense of occasion to every stay.',
                'mission' => 'To make every guest feel genuinely cared for through thoughtful details and sincere hospitality.',
                'vision' => 'To be a trusted destination for restorative stays and memorable gatherings.',
                'contact_address' => '123 Example Avenue, Sample City, Country',
                'contact_phone' => '+1 202-555-0147',
                'contact_email' => 'hello@example.com',
            ],
            'resort' => [
                'title' => 'Find your own pace',
                'body' => 'Settle into a restorative escape with generous spaces, attentive service and room to enjoy the moment.',
                'experiences' => 'Relax by the pool, discover local flavours, or let our concierge help shape a day at your pace.',
            ],
        ] as $key => $value) {
            HotelSetting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        foreach ([
            [
                'name' => 'The Grand Ballroom',
                'venue_type' => 'ballroom',
                'description' => 'A flexible celebration and conference venue with room for a memorable gathering.',
                'capacity' => 300,
            ],
            [
                'name' => 'The Gallery',
                'venue_type' => 'meeting room',
                'description' => 'A refined, light-filled setting for focused meetings and private presentations.',
                'capacity' => 40,
            ],
            [
                'name' => 'The Garden Terrace',
                'venue_type' => 'outdoor venue',
                'description' => 'An open-air setting for receptions, celebrations and intimate ceremonies.',
                'capacity' => 120,
            ],
        ] as $venue) {
            EventVenue::query()->updateOrCreate(['name' => $venue['name']], $venue + ['is_active' => true]);
        }
    }
}
