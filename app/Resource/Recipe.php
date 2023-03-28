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
    public bool $formless = false;
    //public mixed $instance;

    // maybe this will be only for workbench
    // recipes for machines will be in relevant classes?
    const PLATE = [
        'ingredients' => [Component::INGOT,'Forge Hammer'],
        'formless' => true,
        'result' => Component::PLATE
    ];
    const ITEM_CASING = [
        'ingredients' => [Component::PLATE,'Forge Hammer'],
        'formless' => true,
        'amount' =>2,
        'result' => Component::ITEM_CASING
    ];
    const CABLE = [
        'ingredients' => [Component::PLATE,'Cutter'],
        'formless' => true,
        'result' => Component::CABLE
    ];
    const INSULATED_CABLE = [
        'ingredients' => [Component::CABLE,'Resin'],
        'formless' => true,
        'result' => Component::INSULATED_CABLE
    ];

    const BASIC_MACHINE_CASING = [
        'ingredients' => [
            Component::PLATE,Component::PLATE,Component::PLATE,
            Component::PLATE,null,Component::PLATE,
            Component::PLATE,Component::PLATE,Component::PLATE
        ],
        'result' => Component::BASIC_MACHINE_CASING,
    ];
    const DUST = [
        'ingredients' => [
            Component::SMALL_DUST,Component::SMALL_DUST,Component::SMALL_DUST,
            Component::SMALL_DUST,Component::SMALL_DUST,Component::SMALL_DUST,
            Component::SMALL_DUST,Component::SMALL_DUST,Component::SMALL_DUST
        ],
        'result' => Component::DUST
    ];
    const RE_BATTERY = [
        'ingredients' => [
            null,Component::INSULATED_CABLE,null,
            Component::ITEM_CASING,'Red Dust',Component::ITEM_CASING,
            Component::ITEM_CASING,'Red Dust',Component::ITEM_CASING
        ],
        'result' => Component::RE_BATTERY,
    ];
    const ADVANCED_RE_BATTERY = [
        'ingredients' => [
            Component::INSULATED_CABLE,Component::ITEM_CASING,Component::INSULATED_CABLE,
            Component::ITEM_CASING,'Sulfur Dust',Component::ITEM_CASING,
            Component::ITEM_CASING,'Lead Dust',Component::ITEM_CASING
        ],
        'result' => Component::ADVANCED_RE_BATTERY,
    ];

    const MACERATOR = [
        'ingredients' => [
            'Silicate','Silicate','Silicate',
            'Cobblestone',Component::BASIC_MACHINE_CASING,'Cobblestone',
            null,Component::ELECTRONIC_CIRCUIT,null
        ],
        'result' => Processor::MACERATOR,
    ];
    //               Workbench
    // Plate Plate Plate
    // Plate null  Plate  => Basic Machine Casing
    // Plate Plate Plate

    //$ingredients = [
    //Component::PLATE,Component::PLATE,Component::PLATE,
    //Component::PLATE,null,Component::PLATE,
    //Component::PLATE,Component::PLATE,Component::PLATE
    //];
    //$result = Component::BASIC_MACHINE_CASING;
    //$instance = Appliance::WORKBENCH;



    //          Processor::MACERATOR
    // Ore => 2x Crushed Ore

    //$ingredients = [
    //Mineral::ORE
    //];
    //$result = Mineral::CRUSHED_ORE;
    //$amount = 2;
    //$instance = Processor::MACERATOR;



    //Component::INGOT = 'Ingot';
    //Component::DUST = 'Dust';
    //Component::SMALL_DUST = 'Small Dust';

    //Component::RE_BATTERY = 'RE-Battery';
    //Component::ADVANCED_RE_BATTERY = 'Advanced RE-Battery';
    //Component::ENERGY_CRYSTAL = 'Energy Crystal';
    //Component::LAPOTRON_CRYSTAL = 'Lapotron Crystal';

    //Component::PLATE = 'Plate';
    //Component::DENSE_PLATE = 'Dense Plate';
    //Component::CABLE = 'Cable';
    //Component::ITEM_CASING = 'Item Casing';
    //Component::BASIC_MACHINE_CASING = 'Basic Machine Casing';
    //Component::ADVANCED_MACHINE_CASING = 'Advanced Machine Casing';
    //Component::ELECTRONIC_CIRCUIT = 'Electronic Circuit';
    //Component::ADVANCED_CIRCUIT = 'Advanced Circuit';

    //Mineral::ORE = 'Ore';
    //Mineral::CRUSHED_ORE = 'Crushed Ore';
    //Mineral::PURIFIED_CRUSHED_ORE = 'Purified Crushed Ore';


    // Redstone Si(FeS<sub>2</sub>)<sub>5</sub>CrAl<sub>2</sub>O<sub>3</sub>Hg<sub>3</sub>


    // fucked up?
    // 'Generator::GENERATOR'
}
