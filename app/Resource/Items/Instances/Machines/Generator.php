<?php

namespace App\Resource\Items\Instances\Machines;



use App\Resource\Item;
use App\Resource\Items\Instances\Machine;
use App\Resource\Liquid;
use App\Resource\Voltage;

class Generator extends Machine
{
    const GENERATOR = ['name' => 'Generator','production'=>10,'voltage'=> Voltage::LV, 'capacity'=>4000, 'fuel'=>'coal', 'fuelConsumption' => 1/8]; // (coal / wood) => EU/t
    const GEOTHERMAL_GENERATOR = ['name' => 'Geothermal Generator','production'=>20, 'voltage'=> Voltage::LV, 'capacity'=>480000, 'fuel'=>Liquid::LAVA, 'fuelConsumption' => 2];
    const RADIOISOTOPE_THERMOELECTRIC_GENERATOR = ['name' => 'Radioisotope Thermoelectric Generator', 'production'=> 8,'voltage'=> Voltage::LV, 'capacity'=>0, 'fuel'=>'Pellets of RTG Fuel', 'fuelConsumption' => 0];
    const SEMIFLUID_GENERATOR = ['name' => 'Semifluid Generator', 'production'=> 8,'voltage'=> Voltage::LV, 'capacity'=>128000, 'fuel'=>Liquid::OIL, 'fuelConsumption' => 2];
    const SOLAR_PANEL = ['name' => 'Solar Panel', 'production'=> 1,'voltage'=> Voltage::LV, 'capacity'=>0, 'fuel'=>null, 'fuelConsumption' => 0];
    const WATER_MILL = ['name' => 'Water Mill', 'production'=> 2,'voltage'=> Voltage::LV, 'capacity'=>2, 'fuel'=>Liquid::WATER, 'fuelConsumption' => 2];
    const WIND_MILL = ['name' => 'Wind Mill', 'production'=> 5,'voltage'=> Voltage::LV, 'capacity'=>0, 'fuel'=>null, 'fuelConsumption' => 0];
    public int $production;
    public mixed $fuel;
    public int $fuelConsumption;
}
