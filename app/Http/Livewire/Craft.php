<?php

namespace App\Http\Livewire;

use App\Models\InstanceSlot;
use App\Resource\Resource;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Craft extends Component
{
    public array $slots;
    public string $result;
    public function mount()
    {
        // Initialize the $slots array with null values
        $this->slots = array_fill(0, 9, null);
    }
    public function change()
    {
        $slots = collect(array_map(fn($id)=> InstanceSlot::all()->find($id), $this->slots));
        if($recipe = Resource::recipes()->firstWhere('ingredients', $slots->map(fn($slot) => $slot->item()->type))){
            $material = $slots->first()->material;
            $result = Resource::load()
                ->where('type', $recipe->result)
                ->where('material', $material);
            $this->result = $result;
        }

    }
    public function craft()
    {
        dd($this->result);
    }
    public function render()
    {
        return view('livewire.craft');
    }

}
