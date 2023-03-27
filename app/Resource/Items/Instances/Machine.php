<?php

namespace App\Resource\Items;



class Machine extends Instance
{
    public int $capacity;
    public array $upgrades;
    public ?int $voltage;
}
