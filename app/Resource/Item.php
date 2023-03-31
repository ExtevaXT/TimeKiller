<?php

namespace App\Resource;

class Item
{
    public function __construct($data, $merge = null)
    {
        if($merge) $data = array_merge($data, $merge);
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
    public int $id;
    public mixed $type;
    public string $name;
    public mixed $description;
    public int $maxStack;
}
