<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class SubscriptionSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule tasks based on the selected month and day for subscriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subscriptions = Subscription::whereNotNull('frequency_month')
            ->whereNotNull('frequency_day')
            ->get();

        foreach ($subscriptions as $subscription) {
            $month = $subscription->frequency_month;
            $day = $subscription->frequency_day;

            $this->scheduleTask($subscription, $month, $day);
        }
    }

    private function scheduleTask(Subscription $subscription, $month, $day)
    {
        $task = function () use ($subscription) {
            // task logic here, please let me know if this is works for you
        };

        // Schedule the task based on the selected month and day
        $this->task($task)->monthly()->whenMonth($month)->whenDayOfMonth($day);
    }
}
