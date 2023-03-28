<?php

namespace App\Resource\Items;

use App\Resource\Item;

class Mineral extends Item
{
    public float $hardness;
    public mixed $color;
    public mixed $elements;
    public mixed $elementItems;

    const ORE = 'Ore';
    const CRUSHED_ORE = 'Crushed Ore';
    const PURIFIED_CRUSHED_ORE = 'Purified Crushed Ore';
}
