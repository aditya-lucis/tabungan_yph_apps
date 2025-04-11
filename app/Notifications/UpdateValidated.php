<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpdateValidated extends Notification
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
        $statusText = $this->req->status == 1 ? 'Pengajuan pendaftaran tabungan kamu telah disetujui. Sekarang kamu punya skor kredit.' : $this->req->notes . ' untuk melakukan pengajuan tabungan';

        return [
            'message' => $statusText,
            'url' => '#', // Ganti sesuai route
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
