<?php

namespace App\Resource\Items;

use App\Resource\Item;
use App\Resource\Resource;

class Element extends Item
{

    public static function find($symbol)
    {
        return Resource::load()->firstWhere('symbol', $symbol);
    }

    public string $symbol;
    public mixed $color;
    public mixed $melt;
    public mixed $boil;
    public int $mass;
    public bool $metal;
    public bool $radioactive;
}
