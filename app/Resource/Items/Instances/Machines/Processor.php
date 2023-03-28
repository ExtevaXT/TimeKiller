<?php

namespace App\Resource\Items\Instances\Machines;



use App\Resource\Item;
use App\Resource\Items\Instances\Machine;
use App\Resource\Voltage;

class Processor extends Machine
{
    // are you winning son?
    const COMPRESSOR = ['name' => 'Compressor','operation'=>800, 'operationLength'=>20, 'consumption'=> 2, 'voltage'=>Voltage::LV, 'capacity'=>800, 'input'=>null, 'output' => null]; // 9 plates + 625 EU => Dense Plate
    const ELECTRIC_FURNACE = ['name' => 'Electric Furnace','operation'=>390, 'operationLength'=>6.5, 'consumption'=> 3, 'voltage'=>Voltage::LV, 'capacity'=>416, 'input'=>null, 'output' => null];// (Ore / Dust) + 390 EU => Ingot
    const EXTRACTOR = ['name' => 'Extractor','operation'=>800, 'operationLength'=>20, 'consumption'=> 2, 'voltage'=>Voltage::LV, 'capacity'=>800, 'input'=>null, 'output' => null];// Resin + 313 EU => 3 Rubber
    const INDUCTION_FURNACE = ['name' => 'Induction Furnace','operation'=>[6000,208], 'operationLength'=>[18.75,0.65], 'consumption'=> 16, 'voltage'=>Voltage::MV, 'capacity'=>1000, 'input'=>null, 'output' => null];// (Ore / Dust) + 6000 to 208 EU => Ingot
    const MACERATOR = ['name' => 'Macerator','operation'=>600, 'operationLength'=>15, 'consumption'=> 2, 'voltage'=>Voltage::LV, 'capacity'=>4000, 'input'=>null, 'output' => null];// Ore + 625 EU => Crushed Ore
    const METAL_FORMER = ['name' => 'Metal Former','operation'=>2000, 'operationLength'=>10, 'consumption'=> 10, 'voltage'=>Voltage::LV, 'capacity'=>4000, 'input'=>null, 'output' => null];// Ingot + 625 EU => Plates, Item Casings and Wires
    const ORE_WASHING_PLANT = ['name' => 'Ore Washing Plant','operation'=>8000, 'operationLength'=>25, 'consumption'=> 16, 'voltage'=>Voltage::LV, 'capacity'=>16000, 'input'=>null, 'output' => null];// Water + Crushed Ore + 330 EU => Purified Crushed Ore
    const RECYCLER = ['name' => 'Recycler','operation'=>45, 'operationLength'=>2.25, 'consumption'=> 1, 'voltage'=>Voltage::LV, 'capacity'=>45, 'input'=>null, 'output' => null];// Any item + 360 EU => 12.5% Scrap
    const THERMAL_CENTRIFUGE = ['name' => 'Thermal Centrifuge','operation'=>24000, 'operationLength'=>25, 'consumption'=> 48 , 'voltage'=>Voltage::MV, 'capacity'=>48000, 'input'=>null, 'output' => null];// Crushed Ore + (24000 * (mass / multiplier) EU) => Dust + Stone Dust + 1 of elements Small / Tiny dust

    public float $consumption;
    public mixed $operation;
    public mixed $operationLength;
    public Item $input;
    public Item $output;

}
