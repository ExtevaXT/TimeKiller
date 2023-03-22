<?php

namespace App\Resource;

class Resource
{
    public static function elements()
    {
        return collect(json_decode(file_get_contents(resource_path('data/PeriodicTable.json')))->elements);
    }
    public static function minerals()
    {
        return collect(json_decode(file_get_contents(resource_path('data/Minerals.json'))));
    }
}
class Item
{
    public int $id;
    public string $name;
    public string $description;
}
// Need custom logic for them I think
class MachineItem extends Item
{
    public string $type;
    public array $slots;
}
// Base resource element for crafts
class ElementItem extends Item
{
    public string $symbol;
    public string $color;
    public int $melt;
    public int $boil;
    public int $mass;
    public bool $metal;
    public bool $radioactive;
}
// Sum up all numbers and calc percentage of elements in mineral (ore).
class MineralItem extends Item
{
    public int $durability;
    public string $color;
    public string $category;
    public array $elements;
}
// Only for start with Mine
class ToolItem extends Item
{
    public int $durability;
    public int $effectiveness;
}
// Items can be done out of every Metal
class Recipe
{
    public int $id;
    public array $ingredients;
    public Item $result;
}
