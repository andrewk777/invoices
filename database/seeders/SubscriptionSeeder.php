<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\SubscriptionItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subscription::factory()->count(5)->create()->each(function ($subscription) {
            $subscription->charges()->saveMany(SubscriptionItem::factory(2)->create());
            $subscription->save();
        });
    }
}
