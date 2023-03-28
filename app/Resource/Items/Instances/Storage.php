<?php

namespace App\Resource\Items\Instances;


use App\Resource\Items\Instance;

class Storage extends Instance
{
    public mixed $contain; // Liquid / Items / Energy
    public ?int $capacity;
}
