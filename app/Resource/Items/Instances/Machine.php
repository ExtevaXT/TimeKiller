<?php

namespace App\Resource\Items\Instances;



use App\Resource\Items\Instance;

class Machine extends Instance
{
    public int $capacity;
    public array $upgrades;
    public int $voltage;
}
