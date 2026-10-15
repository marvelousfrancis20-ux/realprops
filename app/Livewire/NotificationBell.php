<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    public function markAllRead(): void
    {
        auth()->user()?->unreadNotifications?->markAsRead();
        $this->refreshCount();
    }

    private function refreshCount(): void
    {
        /** @var User|null $user */
        $user = auth()->user();

        $this->unreadCount = $user ? $user->unreadNotifications()->count() : 0;
    }

    public function render()
    {
        $user = auth()->user();

        return view('livewire.notification-bell', [
            'notifications' => $user
                ? $user->notifications()->latest()->take(6)->get()
                : collect(),
        ]);
    }
}
