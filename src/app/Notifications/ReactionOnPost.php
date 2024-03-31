<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReactionOnPost extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Post $post, public User $user)
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
            ->subject('Новая реакция на запись')
            ->line($this->getNotificationText())
            ->action('Перейти к записи', $this->getPostURL());
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
        return 'Пользователь "' . $this->user->username . '" отреагировал на Вашу запись.';
    }

    private function getPostURL(): string
    {
        return url(route('post.view', $this->post->id));
    }
}
