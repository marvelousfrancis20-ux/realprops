<?php

namespace App\Notifications;

use App\Models\TourRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TourRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly TourRequest $tourRequest) {}

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
            ->subject('Tour request: '.$this->tourRequest->property->title)
            ->greeting('Hello '.$notifiable->name.'!')
            ->line($this->tourRequest->user->name.' requested a tour of "'.$this->tourRequest->property->title.'".')
            ->line('Scheduled for '.$this->tourRequest->scheduled_at->format('D, M j Y \\a\\t g:i A'))
            ->when($this->tourRequest->notes, fn (MailMessage $mail) => $mail->line('Notes: '.$this->tourRequest->notes))
            ->action('View request', route('agent.tours.index'))
            ->line('Thank you for using Real Props.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'tour_request',
            'title' => 'New tour request',
            'body' => $this->tourRequest->user->name.' wants to tour '.$this->tourRequest->property->title
                .' on '.$this->tourRequest->scheduled_at->format('M j, g:i A'),
            'url' => route('agent.tours.index'),
            'tour_request_id' => $this->tourRequest->id,
        ];
    }
}
