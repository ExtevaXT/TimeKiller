<?php

namespace App\Resource;

use App\Resource\Items\Component;
use App\Resource\Items\Instance;
use App\Resource\Items\Instances\Machines\Processor;
use App\Resource\Items\Mineral;

class Recipe
{
    public int $id;
    public array $ingredients;
    public mixed $result;
    public int $amount = 1;
    public bool $shapeless = false;
    //public mixed $instance;

    // Redstone Si(FeS<sub>2</sub>)<sub>5</sub>CrAl<sub>2</sub>O<sub>3</sub>Hg<sub>3</sub>
}
