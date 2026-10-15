<?php

namespace App\Notifications;

use App\Models\Property;
use App\Models\SavedSearch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class SavedSearchMatchNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param  Collection<int, Property>  $properties
     */
    public function __construct(
        public readonly SavedSearch $savedSearch,
        public readonly Collection $properties,
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
            ->subject('New matches for "'.$this->savedSearch->name.'"')
            ->greeting('Hello '.$notifiable->name.'!')
            ->line(count($this->properties).' new listing(s) match your saved search "'.$this->savedSearch->name.'".');
        $url = route('properties.index', $this->savedSearch->filters ?? []);

        foreach ($this->properties->take(5) as $property) {
            $mail->line($property->title.' — '.$property->formattedPrice().' in '.$property->city);
        }

        return $mail
            ->action('See matches', $url)
            ->line('Thank you for using Real Props.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'saved_search',
            'title' => 'New matches: '.$this->savedSearch->name,
            'body' => count($this->properties).' new listing(s) match your search.',
            'url' => route('properties.index', $this->savedSearch->filters ?? []),
            'saved_search_id' => $this->savedSearch->id,
        ];
    }
}
