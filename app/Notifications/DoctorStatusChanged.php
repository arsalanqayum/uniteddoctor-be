<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DoctorStatusChanged extends Notification
{
    use Queueable;

    public $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        if ($this->status) {
            $subject = 'Profile Activated';
            $line = 'Your profile has been activated. You are now visible to patients.';
        } else {
            $subject = 'Profile Deactivated';
            $line = 'Your profile has been deactivated. You are no longer visible to patients.';
        }

        return (new MailMessage)
                    ->subject($subject)
                    ->line($line)
                    ->action('View Profile', url('/profile'))
                    ->line('Thank you for using our application!');
    }
}
