<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class BusinessWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    public function backoff(): array
    {
        return [1, 5, 10];
    }

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Http::post($this->url, [
            'status' => $this->status,
            'location' => [
                'lat' => $this->lat,
                'long' => $this->long,
            ]
        ])->throw();
    }

    public function failed(Throwable $exception)
    {
        Log::info("Failed to send webhook to business after {$this->tries} try", [
            'business_id' => $this->businessId,
            'order_id' => $this->orderId,
        ]);
    }
}
