<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FollowedNotification extends Notification
{
    use Queueable;

    protected $follower;

    // تمرير المستخدم الذي يتابع
    public function __construct($follower)
    {
        $this->follower = $follower;
    }

    // القناة التي سيتم إرسال الإشعار من خلالها
    public function via($notifiable)
    {
        return ['mail']; // إرسال عبر البريد الإلكتروني فقط
    }

    // محتوى الإشعار عند الإرسال عبر البريد الإلكتروني
    public function toMail($notifiable)
    {
        return (new MailMessage)
        ->subject('You have a new follower!')
        ->line("You have a new follower: {$this->follower->name}.")
        ->action('View Profile', 'https://yourdomain.com/profile/' . $this->follower->id)
        ->line('Thank you for being a part of our community!');
    }

    // محتوى الإشعار عند الإرسال عبر قاعدة البيانات (اختياري)
    public function toArray($notifiable)
    {
        return [
            'follower_id' => $this->follower->id,
            'follower_name' => $this->follower->name,
        ];
    }
}
