<?php

namespace App\Notifications;

use App\Models\File;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class FileStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $file;
    public $user;
    public $status;

    public function __construct(File $file, $user, $status)
    {
        $this->file = $file;
        $this->user = $user;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'file_name' => $this->file->name,
            'claimed_by' => $this->user->name,
            'status' => $this->status,
        ]);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notification.admin');
    }
}
