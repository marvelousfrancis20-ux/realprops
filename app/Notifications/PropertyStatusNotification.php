<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropertyStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Property $property,
        public readonly string $action,
        public readonly ?string $reason = null,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->subject())
            ->greeting('Hello '.$notifiable->name.'!')
            ->line($this->body());

        if ($this->action === 'rejected' && filled($this->reason)) {
            $mail->line('Reason: '.$this->reason);
        }

        return $mail
            ->action('View listing', route('agent.properties.show', $this->property))
            ->line('Thank you for using Real Props.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'property_status',
            'action' => $this->action,
            'title' => $this->subject(),
            'body' => $this->body(),
            'url' => route('agent.properties.show', $this->property),
            'property_id' => $this->property->id,
        ];
    }

    private function subject(): string
    {
        return match ($this->action) {
            'approved' => 'Listing approved: '.$this->property->title,
            'rejected' => 'Listing needs changes: '.$this->property->title,
            'featured' => 'Listing featured: '.$this->property->title,
            'unfeatured' => 'Listing unfeatured: '.$this->property->title,
            default => 'Listing updated: '.$this->property->title,
        };
    }

    private function body(): string
    {
        return match ($this->action) {
            'approved' => 'Your listing "'.$this->property->title.'" is now live on Real Props.',
            'rejected' => 'Your listing "'.$this->property->title.'" was not approved.',
            'featured' => 'Your listing "'.$this->property->title.'" is now featured.',
            'unfeatured' => 'Your listing "'.$this->property->title.'" is no longer featured.',
            default => 'There was an update to "'.$this->property->title.'".',
        };
    }
}
