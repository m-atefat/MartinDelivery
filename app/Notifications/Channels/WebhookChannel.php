<?php

namespace App\Notifications\Channels;

use Illuminate\Http\Client\RequestException;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class WebhookChannel
{
    /**
     * @throws RequestException
     */
    public function send(object $notifiable, Notification $notification): void
    {
        Http::post($notifiable->routeNotificationForWebhook(), $notification->toWebhook())->throw();
    }
}
