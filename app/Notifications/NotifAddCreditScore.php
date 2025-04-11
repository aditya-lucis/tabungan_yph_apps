<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifAddCreditScore extends Notification
{
    use Queueable;

    protected $req;

    /**
     * Create a new notification instance.
     */
    public function __construct($req)
    {
        $this->req = $req;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable)
    {
        return [
            'message' => 'Pemasukan sebesar ' . number_format($this->req->latestTransaction->credit, 2, ',', ' ') . ' ' . ' untuk ' . $this->req->nama . 
            ' dalam rangka ' . $this->req->latestTransaction->notes,
            'url' => '#',
            'type' => 'request_created'
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
            //
        ];
    }
}
