<?php

namespace App\Resource;

class Item
{
    public function __construct($name, $description = null, $maxStack = 64)
    {
        $this->name = $name;
        $this->description = $description;
        $this->maxStack = $maxStack;
    }
    public int $id;
    public string $name;
    public ?string $description;
    public int $maxStack;
}
