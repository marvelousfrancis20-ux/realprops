<?php

namespace App\Livewire;

use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FavoriteButton extends Component
{
    public Property $property;

    public bool $favorited = false;

    public function mount(Property $property): void
    {
        $this->property = $property;

        $this->favorited = Auth::check()
            && Auth::user()->isFavorite($property);
    }

    public function toggle(): void
    {
        if (! Auth::check()) {
            $this->redirectIntended(route('login'));

            return;
        }

        $user = Auth::user();

        if ($user->isFavorite($this->property)) {
            $user->favorites()->detach($this->property->id);
            $this->favorited = false;

            return;
        }

        $user->favorites()->attach($this->property->id);
        $this->favorited = true;
    }

    public function render()
    {
        return view('livewire.favorite-button');
    }
}
