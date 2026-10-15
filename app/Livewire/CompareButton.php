<?php

namespace App\Livewire;

use App\Models\Property;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class CompareButton extends Component
{
    public Property $property;

    public bool $inCompare = false;

    public function mount(Property $property): void
    {
        $this->property = $property;
        $this->inCompare = in_array($property->id, $this->ids(), true);
    }

    public function toggle(): void
    {
        $ids = $this->ids();

        if (in_array($this->property->id, $ids, true)) {
            $ids = array_values(array_diff($ids, [$this->property->id]));
            $this->inCompare = false;
        } else {
            if (count($ids) >= 4) {
                session()->now('status', 'You can compare up to 4 properties at a time.');

                return;
            }

            $ids[] = $this->property->id;
            $this->inCompare = true;
        }

        Session::put('compare', $ids);

        $this->dispatch('compare-updated', count: count($ids));
    }

    /**
     * @return list<int>
     */
    private function ids(): array
    {
        return array_map('intval', (array) Session::get('compare', []));
    }

    public function render()
    {
        return view('livewire.compare-button');
    }
}
