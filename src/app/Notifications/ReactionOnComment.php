<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReactionOnComment extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Post $post, public Comment $comment, public User $user)
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
            ->subject('Новая реакция на комментарий')
            ->line($this->getNotificationText())
            ->line('"' . $this->comment->comment . '"')
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
        return 'Пользователь "'. $this->user->name .'" отреагировал на Ваш комментарий.';
    }

    private function getPostURL(): string
    {
        return url(route('post.view', $this->post->id));
    }
}
