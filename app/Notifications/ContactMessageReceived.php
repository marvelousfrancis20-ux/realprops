<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactMessageReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly ContactMessage $contactMessage) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Contact form: '.$this->contactMessage->subject)
            ->line('From: '.$this->contactMessage->name.' <'.$this->contactMessage->email.'>'
                .($this->contactMessage->phone ? ' | Phone: '.$this->contactMessage->phone : ''))
            ->line($this->contactMessage->message)
            ->action('Open admin inbox', route('admin.contact-messages.index'))
            ->line('Sent via the Real Props contact form.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'contact',
            'title' => 'New contact message',
            'body' => $this->contactMessage->subject,
            'url' => route('admin.contact-messages.index'),
        ];
    }
}
