<?php

namespace App\Notifications;

use App\Notifications\Channels\LineChannel;
use Illuminate\Notifications\Notification;

class LineTextNotification extends Notification
{
    public function __construct(private string $message) {}

    public function via(mixed $notifiable): array
    {
        return [LineChannel::class];
    }

    public function toLine(mixed $notifiable): string
    {
        return $this->message;
    }
}
