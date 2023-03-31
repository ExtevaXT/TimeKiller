<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Mine extends Component
{
    public mixed $mine;
    public function collect($item)
    {
         dd($item);

    }
    public function render()
    {
        return view('livewire.mine', ['mine' => $this->mine]);
    }
}
