<?php

namespace App\Resource;

class Recipe
{
    public int $id;
    public array $ingredients;
    public Item $result;
    public mixed $workable;
}
