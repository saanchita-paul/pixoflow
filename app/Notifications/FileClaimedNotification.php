<?php

namespace App\Notifications;

use App\Models\File;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class FileClaimedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public File $file;
    public User $user;

    public function __construct(File $file, User $user)
    {
        $this->file = $file;
        $this->user = $user;
    }

    public function via($notifiable): array
    {
        return ['broadcast'];
    }


    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'file_id' => $this->file->id,
            'file_name' => $this->file->name,
            'claimed_by' => $this->user->name,
            'order_id' => $this->file->order_id,
        ]);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notification.admin');
    }

}
