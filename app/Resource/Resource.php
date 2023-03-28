<?php

namespace App\Resource;

use App\Resource\Items\Instances\Machines\Instances\Instances\Instances\Component;
use App\Resource\Items\Instances\Machines\Instances\Instances\Instances\Element;
use App\Resource\Items\Instances\Machines\Instances\Instances\Instances\Generator;
use App\Resource\Items\Instances\Machines\Instances\Instances\Instances\Instance;
use App\Resource\Items\Instances\Machines\Instances\Instances\Instances\Mineral;
use App\Resource\Items\Instances\Machines\Instances\Instances\Instances\Processor;
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
        ['name' => 'Compressor', 'consumption'=> 625, 'voltage'=>Voltage::LV, 'capacity'=>4000, 'input'=>null, 'output' => null],// 9 plates + 625 EU => Dense Plate
        ['name' => 'Electric Furnace', 'consumption'=> 390, 'voltage'=>Voltage::LV, 'capacity'=>4000, 'input'=>null, 'output' => null],// (Ore / Dust) + 390 EU => Ingot
        ['name' => 'Extractor', 'consumption'=> 313, 'voltage'=>Voltage::LV, 'capacity'=>4000, 'input'=>null, 'output' => null],// Resin + 313 EU => 3 Rubber
        ['name' => 'Induction Furnace', 'consumption'=> 6000, 'voltage'=>Voltage::MV, 'capacity'=>48000, 'input'=>null, 'output' => null],// (Ore / Dust) + 6000 to 208 EU => Ingot
        ['name' => 'Macerator', 'consumption'=> 625, 'voltage'=>Voltage::LV, 'capacity'=>4000, 'input'=>null, 'output' => null],// Ore + 625 EU => Crushed Ore
        ['name' => 'Metal Former', 'consumption'=> 625, 'voltage'=>Voltage::LV, 'capacity'=>4000, 'input'=>null, 'output' => null],// Ingot + 625 EU => Plates, Item Casings and Wires
        ['name' => 'Ore Washing Plant', 'consumption'=> 330, 'voltage'=>Voltage::LV, 'capacity'=>4000, 'input'=>null, 'output' => null],// Water + Crushed Ore + 330 EU => Purified Crushed Ore
        ['name' => 'Recycler', 'consumption'=> 360, 'voltage'=>Voltage::LV, 'capacity'=>4000, 'input'=>null, 'output' => null],// Any item + 360 EU => 12.5% Scrap
        ['name' => 'Solar Distiller', 'consumption'=> 0, 'voltage'=>Voltage::LV, 'capacity'=>0, 'input'=>null, 'output' => null],// Sun + Water => Distilled Water
        ['name' => 'Thermal Centrifuge', 'consumption'=> 24000 , 'voltage'=>Voltage::MV, 'capacity'=>48000, 'input'=>null, 'output' => null],// Crushed Ore + (24000 * (mass / multiplier) EU) => Dust + Stone Dust + 1 of elements Small / Tiny dust
        // Purified Crushed Ore + (1500 * (mass / multiplier) EU) => Dust + 2 of elements Small / Tiny dust



        //'Furnace', // (Ore / Dust) + (coal / wood) => Ingot
        //'Mass Fabricator','Pattern Storage', 'Replicator', 'Scanner',

    ];
    const GATHERERS = [
        'Miner','Advanced Miner', 'Pump', 'Advanced Pump'
    ];
    const STORAGES = [
        'Chest','Tank'
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
    public static function make(bool $elementsFilled = false)
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
                foreach (self::GENERATORS as $generator){
                    $instance = new Generator($generator['name']);
                    //$instance->sprite = 'generator.png';
                    $instance->material = $item;
                    $instance->capacity = $generator['capacity'];
                    $instance->voltage = $generator['voltage'];
                    $instance->fuel = $generator['fuel'];
                    $instance->fuelConsumption = $generator['fuelConsumption'];
                    $items->push($instance);
                }
                foreach (self::PROCESSORS as $processor){
                    $instance = new Processor($processor['name']);
                    //$instance->sprite = 'processor.png';
                    $instance->material = $item;
                    $instance->capacity = $processor['capacity'];
                    $instance->consumption = $processor['consumption'];
                    $instance->voltage = $processor['voltage'];
                    $instance->input = $processor['input'];
                    $instance->output = $processor['output'];
                    $items->push($instance);
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
        //file_put_contents(resource_path('data/items.json'), json_encode($items, JSON_PRETTY_PRINT));
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
