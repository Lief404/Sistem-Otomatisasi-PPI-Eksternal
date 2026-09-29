<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class PengajuanTTDNotification extends Notification
{
    use Queueable;

    protected $approval;

    public function __construct($approval)
    {
        $this->approval = $approval;
    }

    public function via(object $notifiable): array
    {
        // Hanya simpan ke database (tabel notifications)
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pengajuan TTD ' . strtoupper($this->approval->jenis_form),
            'message' => 'Mahasiswa (' . $this->approval->mahasiswa_nim . ') mengajukan Tanda Tangan Digital untuk form ' . $this->approval->jenis_form . '.',
            'url' => route('approval.show', $this->approval->token_verifikasi)
        ];
    }
}