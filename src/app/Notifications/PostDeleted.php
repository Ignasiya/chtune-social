<?php

namespace App\Notifications;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostDeleted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Group $group)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Запись удалена')
            ->line($this->getNotificationText())
            ->action('Перейти к группе', $this->getPostURL());
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->getNotificationText(),
            'post_url' => $this->getPostURL(),
        ];
    }

    private function getNotificationText(): string
    {
        return 'Ваша запись удалена в группе "' . $this->group->name . '".';
    }

    private function getPostURL(): string
    {
        return url(route('group.profile', $this->group->slug));
    }
}
