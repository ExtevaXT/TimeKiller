<?php

namespace App\Resource\Items\Instances\Machines;



use App\Resource\Item;
use App\Resource\Items\Instances\Machine;

class Processor extends Machine
{
    public int $consumption;
    public Item $input;
    public Item $output;
}
