<?php

namespace App\Resource\Items;

use App\Resource\Item;

class Instance extends Item
{
    public string $type;
    public array $slots;
    public string $sprite;
    public Element $material;
}
