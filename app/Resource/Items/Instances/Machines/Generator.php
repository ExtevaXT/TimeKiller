<?php

namespace App\Resource\Items\Instances\Machines;



use App\Resource\Item;
use App\Resource\Items\Instances\Machine;

class Generator extends Machine
{
    public int $production;
    public Item $fuel;
    public int $fuelConsumption;

}
