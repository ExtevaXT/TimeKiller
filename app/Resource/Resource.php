<?php

namespace App\Resource;

use App\Resource\Items\ElementItem;
use App\Resource\Items\MineralItem;
use Illuminate\Support\Str;
use function PHPUnit\Framework\lessThanOrEqual;

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
    public static array $ore = ['', 'Crushed', 'Purified Crushed', 'Centrifuged'];
    public static array $processed = ['', 'Small', 'Tiny'];
    public static array $formed = ['Plate', 'Dense Plate', 'Wire', 'Item Casing', 'Gear', 'Ring', 'Rod'];

    // Liquids: Water, Lava, Oil
    // EU - Energy Units
    public static array $generator = [
        'Generator', // (coal / wood) => EU/t
        'Geothermal Generator', // lava => EU/t
        'Nuclear Reactor', 'Reactor Chamber', // uranium pods => EU/t
        'Radioisotope Thermoelectric Generator', // Pellets of RTG Fuel => EU/t
        'Semifluid Generator', // Oil => EU/t
        'Solar Panel', // Sun => EU/t
        'Water Mill', // Water => EU/t
        'Wind Mill', // Height, Weather => EU/t
    ];
    public static array $processor = [
        'Compressor', // 9 plates + 625 EU => Dense Plate
        'Electric Furnace', // (Ore / Dust) + 390 EU => Ingot
        'Extractor', // Resin + 313 EU => 3 Rubber
        'Induction Furnace', // (Ore / Dust) + 6000 to 208 EU => Ingot
        'Furnace', // (Ore / Dust) + (coal / wood) => Ingot
        'Macerator', // Ore + 625 EU => Crushed Ore
        'Mass Fabricator','Pattern Storage', 'Replicator', 'Scanner',
        'Metal Former', // Ingot + 625 EU => Plates, Item Casings and Wires
        'Ore Washing Plant', // Water + Crushed Ore + 330 EU => Purified Crushed Ore
        'Recycler', // Any item + 360 EU => 12.5% Scrap
        'Solar Distiller', // Sun + Water => Distilled Water
        'Thermal Centrifuge', // Crushed Ore + (1500 * (mass / multiplier) EU) => Dust + Stone Dust + 1 of elements Small / Tiny dust
                            // Purified Crushed Ore + (1500 * (mass / multiplier) EU) => Dust + 2 of elements Small / Tiny dust
    ];
    public static array $gatherer = [
        'Miner','Advanced Miner', 'Pump', 'Advanced Pump'
    ];
    public static array $storage = [
        'Chest','Tank'
    ];
    public static function elements()
    {
        return collect(json_decode(file_get_contents(resource_path('data/PeriodicTable.json')))->elements);
    }
    public static function minerals()
    {
        return collect(json_decode(file_get_contents(resource_path('data/Minerals.json'))));
    }

    public static function make()
    {
        $items = collect();
        // Fill up elements and their forms
        foreach (self::elements() as $element){
            $item = new ElementItem($element->name, $element->summary);
            $item->color = $element->{"cpk-hex"};
            $item->symbol = $element->symbol;
            $item->melt = $element->melt;
            $item->boil = $element->boil;
            $item->mass = $element->atomic_mass;
            $item->radioactive = $element->atomic_mass > 207 or in_array($element->atomic_mass, [98, 145]);
            $item->metal = Str::contains($element->category, [' metal', 'lanthanide', 'actinide']);
            $items->push($item);
            if($item->metal) {
                foreach (self::$formed as $form){
                    $item = new Item($element->name.' '.$form, 'Metal '.$form. ' made of '.$element->name);
                    $items->push($item);
                }

            }
            foreach (self::$processed as $form){
                $item = new Item($form.' '.$element->name.' Dust', $form. ' Dust consisting of '.$element->name);
                $items->push($item);
            }
        }

        // Fill up minerals and their forms
        foreach (self::minerals() as $mineral) {
            if(!isset($mineral->formula)
                or Str::contains($mineral->formula, 'g/mol')
                or (float)Str::replace(['<sub>','</sub>'], '', $mineral->formula)
            ) continue;
            $item = new MineralItem($mineral->name, $mineral->formula);
            $item->color = $mineral->color ?? null;
            $formula = $mineral->formula;
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
            foreach ($elements as $symbol => $value){
                $elementItems->push(['element'=>ElementItem::find($symbol), 'percentage' => round(100 * $value / $count, 1)]);
            }
            // Finally
            $item->elements = $elements;
            $item->elementItems = $elementItems;
            $item->hardness = 1;
            $items->push($item);
            foreach (self::$ore as $form){
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
