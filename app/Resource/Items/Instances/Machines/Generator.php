<?php

namespace App\Resource\Items\Instances\Machines;



use App\Resource\Item;
use App\Resource\Items\Instances\Machine;
use App\Resource\Liquid;
use App\Resource\Voltage;

class Generator extends Machine
{
    public int $production;
    public mixed $fuel;
    public mixed $fuelConsumption;
}
