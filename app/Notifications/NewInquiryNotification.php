<?php

namespace App\Notifications;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewInquiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Inquiry $inquiry) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $property = $this->inquiry->property;

        return (new MailMessage)
            ->subject('New inquiry: '.$property->title)
            ->greeting('Hello '.$notifiable->name.'!')
            ->line($this->inquiry->name.' sent an inquiry about "'.$property->title.'".')
            ->line($this->inquiry->message)
            ->line('Reply directly from your inbox: '.route('agent.inquiries.show', $this->inquiry))
            ->action('View inquiry', route('agent.inquiries.show', $this->inquiry))
            ->line('Thank you for using Real Props.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'inquiry',
            'title' => 'New inquiry',
            'body' => $this->inquiry->name.' is interested in '.$this->inquiry->property->title,
            'url' => route('agent.inquiries.show', $this->inquiry),
            'inquiry_id' => $this->inquiry->id,
        ];
    }
}
