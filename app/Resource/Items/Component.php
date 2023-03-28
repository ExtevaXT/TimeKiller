<?php


namespace App\Resource\Items;


use App\Resource\Item;

class Component extends Item
{
    public Item $material;

    const INGOT = 'Ingot';
    const DUST = 'Dust';
    const SMALL_DUST = 'Small Dust';

    const RE_BATTERY = 'RE-Battery';
    const ADVANCED_RE_BATTERY = 'Advanced RE-Battery';
    const ENERGY_CRYSTAL = 'Energy Crystal';
    const LAPOTRON_CRYSTAL = 'Lapotron Crystal';

    const PLATE = 'Plate';
    const DENSE_PLATE = 'Dense Plate';
    const CABLE = 'Cable';
    const INSULATED_CABLE = 'Cable';
    const ITEM_CASING = 'Item Casing';
    const BASIC_MACHINE_CASING = 'Basic Machine Casing';
    const ADVANCED_MACHINE_CASING = 'Advanced Machine Casing';
    const ELECTRONIC_CIRCUIT = 'Electronic Circuit';
    const ADVANCED_CIRCUIT = 'Advanced Circuit';
}
