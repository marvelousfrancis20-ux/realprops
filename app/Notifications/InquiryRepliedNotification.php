<?php

namespace App\Notifications;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InquiryRepliedNotification extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('Agent replied: '.$this->inquiry->property->title)
            ->greeting('Hello '.$notifiable->name.'!')
            ->line($this->inquiry->agent->name.' replied to your inquiry about "'.$this->inquiry->property->title.'".')
            ->line($this->inquiry->reply_message)
            ->action('View conversation', route('inquiries.index'))
            ->line('Thank you for using Real Props.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'inquiry_reply',
            'title' => 'Agent replied',
            'body' => $this->inquiry->agent->name.' replied about '.$this->inquiry->property->title,
            'url' => route('inquiries.index'),
            'inquiry_id' => $this->inquiry->id,
        ];
    }
}
