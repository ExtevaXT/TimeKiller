<?php

namespace App\Resource\Items\Instances\Machines;



use App\Resource\Item;
use App\Resource\Items\Instances\Machine;
use App\Resource\Voltage;

class Gatherer extends Machine
{
    public float $consumption;
    public mixed $operation;
    public mixed $operationLength;
    public mixed $input;
    public mixed $output;

}
