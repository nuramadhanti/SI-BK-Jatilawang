<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;

class PermohonanKonselingNotification extends Notification
{
    use Queueable;

    protected $permohonan;
    protected $message;
    protected $type; // 'new', 'approved', 'rejected', 'completed'

    public function __construct($permohonan, $message, $type = 'new')
    {
        $this->permohonan = $permohonan;
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Determine which channels to send notification to
     * Send email for important notifications (new urgent, rejected, approved)
     */
    public function via($notifiable)
    {
        // Send email for urgent cases or important events
        if (in_array($this->type, ['new', 'rejected', 'approved']) || 
            ($this->permohonan->skor_prioritas && $this->permohonan->skor_prioritas >= 70)) {
            return ['database', 'mail'];
        }
        return ['database'];
    }

    /**
     * Database Notification Format
     */
    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'message' => $this->message,
            'type' => $this->type,
            'permohonan_id' => $this->permohonan->id,
            'siswa_name' => $this->permohonan->siswa->user->name,
            'siswa_id' => $this->permohonan->siswa_id,
            'status' => $this->permohonan->status,
            'skor_prioritas' => $this->permohonan->skor_prioritas,
            'kategori_masalah' => $this->permohonan->kategori_masalah_label,
            'alasan_penolakan' => $this->permohonan->alasan_penolakan,
            'guru_bk_id' => $this->permohonan->guru_bk_id,
            'action_url' => route('permohonan-konseling.show', $this->permohonan->id),
        ]);
    }

    /**
     * Email Notification Format
     */
    public function toMail($notifiable)
    {
        $subject = match($this->type) {
            'new' => '🔔 [URGENT] Permohonan Konseling Baru Masuk - Skor: ' . ($this->permohonan->skor_prioritas ?? 'N/A'),
            'approved' => '✅ Permohonan Konseling Anda Telah Disetujui',
            'rejected' => '❌ Permohonan Konseling Anda Ditolak',
            'completed' => '🎉 Permohonan Konseling Anda Telah Selesai',
            default => 'Update Permohonan Konseling'
        };

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line($this->message);

        // Add context-specific information
        if ($this->type === 'new') {
            $mail->line('---')
                 ->line('📊 <strong>Skor Prioritas:</strong> ' . ($this->permohonan->skor_prioritas ?? 'Pending'))
                 ->line('🏷️ <strong>Kategori Masalah:</strong> ' . ($this->permohonan->kategori_masalah_label ?? 'N/A'))
                 ->line('👤 <strong>Siswa:</strong> ' . $this->permohonan->siswa->user->name)
                 ->line('📝 <strong>Permasalahan:</strong> ' . substr($this->permohonan->deskripsi_permasalahan, 0, 100) . '...')
                 ->line('---');
        }

        if ($this->type === 'rejected' && $this->permohonan->alasan_penolakan) {
            $mail->line('---')
                 ->line('<strong style="color: #d32f2f;">📝 Alasan Penolakan:</strong>')
                 ->line($this->permohonan->alasan_penolakan)
                 ->line('---');
        }

        if ($this->type === 'approved') {
            $mail->line('---')
                 ->line('✅ Permohonan Anda telah diterima dan akan diproses.')
                 ->line('Silakan hubungi guru BK untuk informasi jadwal konseling lebih lanjut.')
                 ->line('---');
        }

        return $mail->action('Lihat Permohonan', 
            route('permohonan-konseling.show', $this->permohonan->id))
            ->line('Terima kasih telah menggunakan sistem SI-BK Jatilawang.');
    }
}

