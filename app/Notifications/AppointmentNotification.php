<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;

class AppointmentNotification extends Notification
{
    use Queueable;
    public $apt, $user,$link;
    /**
     * Create a new notification instance.
     */
    public function __construct($appointment, $user,$link)
    {
        $this->apt = $appointment;
        $this->user = $user;
        $this->link =$link;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $con=$this->link;
        return (new MailMessage)
            ->line($this->apt)
            ->when($con,function($q) use($con){
                $q->action('Online Meeting Link', url($con));
            })
            ->line('Thank you for using our aladoc!');
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
