<?php

namespace App\Notifications;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Notifications\Channels\WebhookChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BusinessOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly Order $order)
    {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebhookChannel::class];
    }

    public function toWebhook()
    {
        return json_decode(OrderResource::make($this->order)->toJson(), true);
    }

    public function viaQueues(): array
    {
        return [
            WebhookChannel::class => 'webhook-queue'
        ];
    }
}
