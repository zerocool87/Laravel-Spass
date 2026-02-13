<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentActionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $subject,
        public string $line,
        public string $actionLabel,
        public string $actionUrl,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->subject)
            ->line($this->line)
            ->action($this->actionLabel, $this->actionUrl);
    }
}
