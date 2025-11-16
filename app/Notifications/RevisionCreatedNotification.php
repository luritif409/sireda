<?php

namespace App\Notifications;

use App\Models\Revision;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RevisionCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Revision $revision)
    {
    }

    public function via(object $notifiable): array
    {
        // database channel only (simple)
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'revision_created',
            'revision_id' => $this->revision->id,
            'message' => 'Revisi baru dibuat oleh dosen '.$this->revision->dosen->name,
            'status' => $this->revision->status,
            'tahap' => $this->revision->tahap,
        ];
    }
}












