<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FollowUser extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $follower, public bool $follow = false)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->follow) {
            $subject = 'Новый подписчик';
        } else {
            $subject = 'Пользователь отписался';
        }
        return (new MailMessage)
            ->subject($subject)
            ->lineIf($this->follow, 'Пользователь "' . $this->follower->username .'" подписался на Вас.')
            ->lineIf(!$this->follow, 'Пользователь "' . $this->follower->username .'" отписался от Вас.')
            ->action('Перейти в профиль', url(route('profile', $this->follower)));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
