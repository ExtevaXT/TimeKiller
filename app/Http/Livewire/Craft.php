<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Craft extends Component
{
    public array $slots;
    public function craft()
    {
        dd($this->slots);
    }
    public function render()
    {
        $this->slots = array_fill(0, 9, null);
        return view('livewire.craft');
    }
}
