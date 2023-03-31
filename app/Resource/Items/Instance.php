<?php

namespace App\Resource\Items;

use App\Resource\Item;
use JetBrains\PhpStorm\Pure;

class Instance extends Item
{
    // looks nice
    #[Pure] public function __construct($data, $merge = null) {
        parent::__construct($data, array_merge($merge, ['type' => $data['name']]));
    }
    public mixed $type;
    public array $slots;
    public string $sprite;
    public mixed $material;
}
