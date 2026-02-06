<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;

class ReportNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;

    public function __construct($title, $message)
    {
        $this->title = $title;
        $this->message = $message;
    }

    // کانال‌های ارسال
    public function via($notifiable)
    {
        return ['database', 'broadcast', 'fcm'];
    }

    // داده‌های ذخیره‌شده در دیتابیس
    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
        ];
    }

    // نوتیفیکیشن Broadcast برای وب
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => $this->title,
            'message' => $this->message,
        ]);
    }

    // نوتیفیکیشن برای اپ موبایل (Firebase Push)
    public function toFcm($notifiable)
    {
        return [
            'notification' => [
                'title' => $this->title,
                'body' => $this->message,
            ],
            'data' => [
                'type' => 'report',
            ],
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject($this->title)
                    ->line($this->message);
    }
}
