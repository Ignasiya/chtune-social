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
        return ['mail', 'database'];
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
            ->lineIf($this->follow, $this->getNotificationText('подписался на'))
            ->lineIf(!$this->follow, $this->getNotificationText('отписался от'))
            ->action('Перейти в профиль', $this->getPostURL());
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if (!!$this->follow) {
            $text = 'подписался на';
        } else {
            $text = 'отписался от';
        }
        return [
            'message' => $this->getNotificationText($text),
            'post_url' => $this->getPostURL(),
        ];
    }

    private function getNotificationText(string $text): string
    {
        return 'Пользователь "' . $this->follower->username . '" ' . $text .' Вас.';
    }

    private function getPostURL(): string
    {
        return url(route('profile', $this->follower));
    }
}
