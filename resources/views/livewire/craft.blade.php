<div style="display: flex; align-items: center">
    <div style="display: grid; gap: 1px; grid-template-columns: repeat(3,1fr)">
        @for($i = 0; $i<9; $i++)
            <select wire:model="{{$slots[$i]}}">
                <option value="1">Iron</option>
                <option value="2">Copper</option>
            </select>
        @endfor
    </div>
    <div>=></div>
    <div wire:click="craft">
        <div style="width: 32px; height: 32px; border: black solid 1px"></div>
        <div>Result Item</div>
    </div>

</div>
