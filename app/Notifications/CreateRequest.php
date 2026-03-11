<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreateRequest extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private $customerId;

    private $content;
    public function __construct($customerId, $content)
    {
        $this->customerId = $customerId;
        $this->content=$content;
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
     * Get the database representation of the notification.
     */

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'customerId' =>$this->customerId,
            'content' => $this->content
        ];
    }
}
