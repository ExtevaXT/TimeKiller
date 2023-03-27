<?php

namespace App\Resource\Items;



use App\Resource\Item;

class Processor extends Machine
{
    public int $consumption;
    public Item $input;
    public Item $output;
}
