<?php

namespace App\Resource\Items\Instances;


use App\Resource\Item;
use App\Resource\Items\Instance;

class Appliance extends Instance
{
    public Item $input;
    public Item $output;
    public Item $fuel;
}
