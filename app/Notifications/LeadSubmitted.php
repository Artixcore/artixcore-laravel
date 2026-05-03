<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadSubmitted extends Notification
{
    use Queueable;

    public function __construct(
        public Lead $lead,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'New lead #'.$this->lead->id.' — '.$this->lead->service_type;

        return (new MailMessage)
            ->subject($subject)
            ->line('A new project request was submitted through the website.')
            ->line('**Name:** '.$this->lead->name)
            ->line('**Email:** '.$this->lead->email)
            ->line('**Phone:** '.($this->lead->phone ?: '—'))
            ->line('**Service:** '.$this->lead->service_type)
            ->line('**Message:**')
            ->line($this->lead->message)
            ->action('View in admin', route('admin.leads.show', $this->lead, absolute: true));
    }
}
