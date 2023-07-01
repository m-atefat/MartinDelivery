<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\Delivery;
use Illuminate\Console\Command;

class InitialProjectForTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:initial-project-for-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'initial project for test';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /** @var Delivery $delivery */
        $delivery = Delivery::factory([
            'phone' => '09121234569'
        ])->create();
        $deliveryToken = $delivery->createToken('delivery', ['delivery'])->plainTextToken;

        /** @var Business $business */
        $business = Business::factory()->create();
        $businessToken = $business->createToken('business', ['business'])->plainTextToken;

        $this->info('Delivery Token: ');
        $this->info($deliveryToken);

        $this->info('Business Token: ');
        $this->info($businessToken);
    }
}
