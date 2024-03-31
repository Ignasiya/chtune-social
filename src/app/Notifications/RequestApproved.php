<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestApproved extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Group $group, public User $user, public string $approved)
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
            ->subject('Запрос ' . $this->approved)
            ->line($this->getNotificationText())
            ->action('Перейти в группу', $this->getPostURL());
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
        return 'Ваш запрос на вступление в группу "' . $this->group->name . '" ' . $this->approved;
    }

    private function getPostURL(): string
    {
        return url(route('group.profile', $this->group->slug));
    }
}
