<?php

namespace App\Http\Livewire;

use App\Resource\Resource;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Mine extends Component
{
    public mixed $mine;
    public function collect($id)
    {
         dd(Resource::find($id));

    }
    public function render()
    {
        return view('livewire.mine', ['mine' => $this->mine]);
    }
}
