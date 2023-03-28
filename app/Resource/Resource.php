<?php

namespace App\Resource;

use App\Resource\Items\Component;
use App\Resource\Items\Element;
use App\Resource\Items\Instance;
use App\Resource\Items\Instances\Storage;
use App\Resource\Items\Mineral;
use App\Resource\Items\Instances\Machines\Generator;
use App\Resource\Items\Instances\Machines\Processor;
use App\Resource\Items\Instances\Machines\EnergyStorage;
use Illuminate\Support\Str;

// Mine array
// □□□□□□□□□□
// ■■□□■□□□□□
// □■■■■□□□□□
// □□□□■□□□□□
// ..........

// 1 - stone, 5 - some mineral
// generates down 255 times
// at least one ore vein
$example = [
    [1,1,1,1,1,1,1,1,1,1],
    [5,5,1,1,5,1,1,1,1,1],
    [1,5,5,5,5,1,1,1,1,1],
    [1,1,1,1,5,1,1,1,1,1]
];
// plot acts same but there is also slot id
class Resource
{
    const TPS = 20;
    const ORE = ['', 'Crushed', 'Purified Crushed', 'Centrifuged'];
    const PROCESSED = ['', 'Small', 'Tiny'];
    const FORMED = ['Plate', 'Dense Plate', 'Wire', 'Item Casing', 'Gear', 'Ring', 'Rod'];

    // Liquids: Water, Lava, Oil
    // EU - Energy Units
    // t - Tick, 1t - 1/20s
    const GENERATORS = [
        ['name' => 'Generator','production'=>10,'voltage'=> Voltage::LV, 'capacity'=>4000, 'fuel'=>'coal', 'fuelConsumption' => 1/8], // (coal / wood) => EU/t
        ['name' => 'Geothermal Generator','production'=>20, 'voltage'=> Voltage::LV, 'capacity'=>480000, 'fuel'=>Liquid::LAVA, 'fuelConsumption' => 2],
        ['name' => 'Radioisotope Thermoelectric Generator', 'production'=> 8,'voltage'=> Voltage::LV, 'capacity'=>0, 'fuel'=>'Pellets of RTG Fuel', 'fuelConsumption' => 0],
        ['name' => 'Semifluid Generator', 'production'=> 8,'voltage'=> Voltage::LV, 'capacity'=>128000, 'fuel'=>Liquid::OIL, 'fuelConsumption' => 2],
        ['name' => 'Solar Panel', 'production'=> 1,'voltage'=> Voltage::LV, 'capacity'=>0, 'fuel'=>null, 'fuelConsumption' => 0],
        ['name' => 'Water Mill', 'production'=> 2,'voltage'=> Voltage::LV, 'capacity'=>2, 'fuel'=>Liquid::WATER, 'fuelConsumption' => 2],
        ['name' => 'Wind Mill', 'production'=> 5,'voltage'=> Voltage::LV, 'capacity'=>0, 'fuel'=>null, 'fuelConsumption' => 0],
        //'Nuclear Reactor', 'Reactor Chamber', // uranium pods => EU/t
    ];
    const PROCESSORS = [
        ['name' => 'Compressor','operation'=>800, 'operationLength'=>20, 'consumption'=> 2, 'voltage'=>Voltage::LV, 'capacity'=>800, 'input'=>null, 'output' => null],// 9 plates + 625 EU => Dense Plate
        ['name' => 'Electric Furnace','operation'=>390, 'operationLength'=>6.5, 'consumption'=> 3, 'voltage'=>Voltage::LV, 'capacity'=>416, 'input'=>null, 'output' => null],// (Ore / Dust) + 390 EU => Ingot
        ['name' => 'Extractor','operation'=>800, 'operationLength'=>20, 'consumption'=> 2, 'voltage'=>Voltage::LV, 'capacity'=>800, 'input'=>null, 'output' => null],// Resin + 313 EU => 3 Rubber
        ['name' => 'Induction Furnace','operation'=>[6000,208], 'operationLength'=>[18.75,0.65], 'consumption'=> 16, 'voltage'=>Voltage::MV, 'capacity'=>1000, 'input'=>null, 'output' => null],// (Ore / Dust) + 6000 to 208 EU => Ingot
        ['name' => 'Macerator','operation'=>600, 'operationLength'=>15, 'consumption'=> 2, 'voltage'=>Voltage::LV, 'capacity'=>4000, 'input'=>null, 'output' => null],// Ore + 625 EU => Crushed Ore
        ['name' => 'Metal Former','operation'=>2000, 'operationLength'=>10, 'consumption'=> 10, 'voltage'=>Voltage::LV, 'capacity'=>4000, 'input'=>null, 'output' => null],// Ingot + 625 EU => Plates, Item Casings and Wires
        ['name' => 'Ore Washing Plant','operation'=>8000, 'operationLength'=>25, 'consumption'=> 16, 'voltage'=>Voltage::LV, 'capacity'=>16000, 'input'=>null, 'output' => null],// Water + Crushed Ore + 330 EU => Purified Crushed Ore
        ['name' => 'Recycler','operation'=>45, 'operationLength'=>2.25, 'consumption'=> 1, 'voltage'=>Voltage::LV, 'capacity'=>45, 'input'=>null, 'output' => null],// Any item + 360 EU => 12.5% Scrap
        //['name' => 'Solar Distiller','operation'=>800, 'operationLength'=>20, 'consumption'=> 2, 'voltage'=>Voltage::LV, 'capacity'=>0, 'input'=>null, 'output' => null],// Sun + Water => Distilled Water
        ['name' => 'Thermal Centrifuge','operation'=>24000, 'operationLength'=>25, 'consumption'=> 48 , 'voltage'=>Voltage::MV, 'capacity'=>48000, 'input'=>null, 'output' => null],// Crushed Ore + (24000 * (mass / multiplier) EU) => Dust + Stone Dust + 1 of elements Small / Tiny dust
        // Purified Crushed Ore + (1500 * (mass / multiplier) EU) => Dust + 2 of elements Small / Tiny dust


        //'Furnace', // (Ore / Dust) + (coal / wood) => Ingot
        //'Mass Fabricator','Pattern Storage', 'Replicator', 'Scanner',
    ];
    const GATHERERS = [
        ['name' => 'Miner','operation'=>1000, 'operationLength'=>30, 'consumption'=> 12 , 'voltage'=>Voltage::LV, 'capacity'=>1000, 'input'=>null, 'output' => null],
        ['name' => 'Advanced Miner','operation'=>512, 'operationLength'=>1, 'consumption'=> 25.6 , 'voltage'=>Voltage::HV, 'capacity'=>4000000, 'input'=>null, 'output' => null],
        ['name' => 'Pump','operation'=>1000, 'operationLength'=>1, 'consumption'=> 2 , 'voltage'=>Voltage::LV, 'capacity'=>200, 'input'=>null, 'output' => null],
        ['name' => 'Advanced Pump','operation'=>512, 'operationLength'=>1, 'consumption'=> 25.6 , 'voltage'=>Voltage::HV, 'capacity'=>4000000, 'input'=>null, 'output' => null],
    ];
    const STORAGES = [
        ['name' => 'Chest','capacity'=>32],
        ['name' => 'Tank','capacity'=>32000],
    ];
    const ENERGY_STORAGES = [
        ['name'=>'BatBox','capacity'=>40000, 'voltage'=>32],
        ['name'=>'CESU','capacity'=>300000, 'voltage'=>128],
        ['name'=>'MFE','capacity'=>4000000, 'voltage'=>512],
        ['name'=>'MFSU','capacity'=>4000000, 'voltage'=>2048],
    ];

    public static function elements()
    {
        return collect(json_decode(file_get_contents(resource_path('data/PeriodicTable.json')))->elements);
    }
    public static function minerals()
    {
        return collect(json_decode(file_get_contents(resource_path('data/Minerals.json'))));
    }
    public static function find($id)
    {
        return Resource::load()->firstWhere('id', $id);
    }
    public static function make(bool $elementsFilled = false, $json = false)
    {
        $items = collect();
        // Fill up Elements
        foreach (self::elements() as $element){
            $item = new Element($element->name, $element->summary);
            $item->color = $element->{"cpk-hex"};
            $item->symbol = $element->symbol;
            $item->melt = $element->melt;
            $item->boil = $element->boil;
            $item->mass = $element->atomic_mass;
            $item->radioactive = $element->atomic_mass > 207 or in_array($element->atomic_mass, [98, 145]);
            $item->metal = Str::contains($element->category, [' metal', 'lanthanide', 'actinide']);
            $items->push($item);
            // Fill up Items made of this Element
            if($item->metal) {
                foreach (self::FORMED as $form){
                    $component = new Component($element->name.' '.$form, 'Metal '.$form. ' made of '.$element->name);
                    $component->material = $item;
                    $items->push($component);
                }
                foreach (self::GENERATORS as $instance){
                    $generator = new Generator($item->name.' '.$instance['name']);
                    $generator->type = $instance;
                    $generator->material = $item;
                    $generator->production = $instance['production'];
                    $generator->voltage = $instance['voltage'];
                    $generator->capacity = $instance['capacity'];
                    $generator->fuel = $instance['fuel'];
                    $generator->fuelConsumption = $instance['fuelConsumption'];
                    $items->push($generator);
                }
                foreach (self::PROCESSORS as $instance){
                    $processor = new Processor($item->name.' '.$instance['name']);
                    $processor->type = $instance;
                    $processor->material = $item;
                    $processor->operation = $instance['operation'];
                    $processor->operationLength = $instance['operationLength'];
                    $processor->capacity = $instance['capacity'];
                    $processor->consumption = $instance['consumption'];
                    $processor->voltage = $instance['voltage'];
                    $items->push($processor);
                }
                foreach (self::GATHERERS as $instance){
                    $processor = new Processor($item->name.' '.$instance['name']);
                    $processor->type = $instance;
                    $processor->material = $item;
                    $processor->operation = $instance['operation'];
                    $processor->operationLength = $instance['operationLength'];
                    $processor->capacity = $instance['capacity'];
                    $processor->consumption = $instance['consumption'];
                    $processor->voltage = $instance['voltage'];

                    $items->push($processor);
                }
                foreach (self::STORAGES as $instance){
                    $storage = new Storage($item->name.' '.$instance['name']);
                    $storage->type = $instance;
                    $storage->material = $item;
                    $storage->capacity = $instance['capacity'];
                    $items->push($storage);
                }
                foreach (self::ENERGY_STORAGES as $instance){
                    $storage = new EnergyStorage($item->name.' '.$instance['name']);
                    $storage->type = $instance;
                    $storage->material = $item;
                    $storage->capacity = $instance['capacity'];
                    $storage->voltage = $instance['voltage'];
                    $items->push($storage);
                }
            }
            foreach (self::PROCESSED as $form){
                $component = new Component($form.' '.$element->name.' Dust', $form. ' Dust consisting of '.$element->name);
                $component->material = $item;
                $items->push($component);
            }


        }
        // Fill up Minerals and their forms
        foreach (self::minerals() as $mineral) {
            if(!isset($mineral->formula)
                or Str::contains($mineral->formula, 'g/mol')
                or (float)Str::replace(['<sub>','</sub>'], '', $mineral->formula)
            ) continue;
            $item = new Mineral($mineral->name, $mineral->formula);
            $item->color = $mineral->color ?? null;
            $formula = $mineral->formula;
            #region Formula to elements
            // Need to break this formula to elements with percentage
            // (Na,K,Ca)<sub>2</sub>Al<sub>2</sub>Si<sub>7</sub>O<sub>18</sub>·<sub>6</sub>(H<sub>2</sub>O)
            // ['Na' => 2, 'K' => 2, 'Ca' => 2, 'Al' => 2, 'Si' => 7, 'O' => 18, 'H' => 2*6, 'O' = 1*6]
            // Zn<sub>2</sub>AsO<sub>4</sub>OH
            // NaY(CO<sub>3</sub>)<sub>2</sub>·<sub>6</sub>H<sub>2</sub>O
            $formula = explode('</sub>',$formula);
            $elements = collect();
            foreach ($formula as $element){
                $element = explode( '<sub>',$element);
                if(Str::contains($element[0], ',')){
                    $multiple = explode(',', Str::replace(['(',')'], '', $element[0]));
                    $multiple = array_map(function($el) {
                        // IT WORKS
                        $els = preg_split('/(?=[A-Z])/',$el, -1, PREG_SPLIT_NO_EMPTY);
                        if(count($els) == 1)
                            return [Str::replace(['(',')'], '', $el)=>((int)($element[1] ?? 1))];
                        else{
                            return array_map(function($elm) {
                                return [Str::replace(['(',')'], '', $elm)=>((int)($element[1] ?? 1))];
                            }, $els);
                        }
                    },$multiple);

                    $elements->push($multiple);
                }
                else{
                    // Fucking chemistry. Idk how to multiply next compound with this
                    if(Str::contains($element[0],'·')) continue;
                    $multiple = preg_split('/(?=[A-Z])/',$element[0]);
                    unset($multiple[0]);
                    if(count($multiple) == 1){
                        $element = [Str::replace(['(',')'], '', $element[0]) => (int)($element[1] ?? 1)];
                        $elements->push($element);
                    }
                    else{
                        $multiple = array_map(fn($el) => [Str::replace(['(',')'], '', $el)=>((int)($element[1] ?? 1))], $multiple);
                        $elements->push($multiple);
                    }
                }
            }
            // Fucking php. Idk how this works
            $elements = self::flatten($elements->toArray());
            $elements = array_reduce($elements, 'array_merge_recursive', array());
            $elements = array_map(function ($value){
                if(is_array($value)) $value = array_sum($value);
                return $value;
            }, $elements);
            //[['K'=>2], ['U'=>2], ['O'=>2], ['As'=>4], ['O'=>4], ['H'=>2], ['O'=>1]]
            //['K'=>2, 'U'=>2, 'O'=>2, 'As'=>4, 'O'=>4, 'H'=>2, 'O'=>1]
            //['K'=>2, 'U'=>2,'O'=>7, 'As'=>4, 'H'=>2]

            // Count percentage
            $count = array_sum($elements);
            // Need to assign ElementItem instances
            $elementItems = collect();
            if($elementsFilled)
                foreach ($elements as $symbol => $value)
                    $elementItems->push(['element'=>Element::find($symbol), 'percentage' => round(100 * $value / $count, 1)]);
            // Finally
            #endregion
            $item->elements = $elements;
            $item->elementItems = $elementItems;
            $item->hardness = 1;
            $items->push($item);
            foreach (self::ORE as $form){
                $item = new Item($form.' '.$mineral->name.' Ore', $mineral->formula);
                $items->push($item);
            }
        }
        $items->map(fn($item, $i) => $item->id = $i + 1);
        if($json) file_put_contents(resource_path('data/items.json'), json_encode($items, JSON_PRETTY_PRINT));
        file_put_contents(resource_path('data/items'), serialize($items));
    }
    public static function flatten($array): array
    {
        $return = [];
        array_walk_recursive($array, function ($b, $a) use (&$return) {
            $return[] = [$a => $b];
        });
        return $return;
    }
    public static function load()
    {
        return unserialize(file_get_contents(resource_path('data/items')));

    }
}

// Need custom logic for them I think
// Base resource element for crafts
// Sum up all numbers and calc percentage of elements in mineral (ore).
// Only for start with Mine
// Items can be done out of every Metal
