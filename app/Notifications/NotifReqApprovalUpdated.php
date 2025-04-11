<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifReqApprovalUpdated extends Notification
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
    public function toDatabase($notifiable)
    {
        $statusText = $this->req->status == 1 ? 'Pengajuan pencairan dana tabungan kamu telah disetujui' : 'Maaf engajuan pencairan dana tabungan kamu tidak disetujui';

        return [
            'message' => $statusText,
            'url' => route('tabungan.inbox'), // Ganti sesuai route
            'type' => 'request_updated',
            'status' => $this->req->status
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
