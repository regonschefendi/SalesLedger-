<?php

namespace App\Notifications;

use App\Notifications\Channels\WebPushChannel;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    public function __construct(
        public string $title,
        public string $message,
        public ?string $url = null,
        public string $type = 'info'
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class];
    }

    /**
     * Payload untuk Web Push API (tampil di luar aplikasi / lockscreen).
     *
     * @return array<string, mixed>
     */
    public function toWebPush(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->message,
            'url' => $this->url ?? '/notifications',
            'type' => $this->type,
            'tag' => 'sales-ledger-'.md5($this->type.'|'.$this->title.'|'.$this->message),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'type' => $this->type,
        ];
    }
}
