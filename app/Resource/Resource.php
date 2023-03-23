<?php

namespace App\Resource;

use Illuminate\Support\Str;
use function PHPUnit\Framework\lessThanOrEqual;


class Resource
{
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
        $ore = ['Crushed Ore', 'Purified Crushed Ore', 'Centrifuged Ore'];
        $processed = ['Dust', 'Small Dust', 'Tiny Dust'];
        $formed = ['Plate', 'Dense Plate', 'Wire', 'Item Casing', 'Gear', 'Ring', 'Rod'];

        $items = collect();
        // Fill up elements and their forms
        foreach (self::elements() as $element){
            $item = new ElementItem();
            $item->name = $element->name;
            $item->description = $element->summary;
            $item->color = $element->{"cpk-hex"};
            $item->symbol = $element->symbol;
            $item->melt = $element->melt;
            $item->boil = $element->boil;
            $item->mass = $element->atomic_mass;
            $item->radioactive = $element->atomic_mass > 207 or in_array($element->atomic_mass, [98, 145]);
            $item->metal = Str::contains($element->category, [' metal', 'lanthanide', 'actinide']);
            $items->push($item);
            if($item->metal) {
                foreach ($formed as $form){
                    $item = new Item();
                    $item->name = $element->name.' '.$form;
                    $item->description = 'Metal '.$form. ' made of '.$element->name;
                    $items->push($item);
                }
            }
            foreach ($processed as $form){
                $item = new Item();
                $item->name = $element->name.' '.$form;
                $item->description = $form. ' consisting of '.$element->name;
                $items->push($item);
            }
        }

        // Fill up minerals and their forms
        foreach (self::minerals() as $mineral) {
            if(!isset($mineral->formula)
                or Str::contains($mineral->formula, 'g/mol')
                or (float)Str::replace(['<sub>','</sub>'], '', $mineral->formula)
            ) continue;
            $item = new MineralItem();
            $item->name = $mineral->name;
            $item->description = $mineral->formula;
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
            $elements = array_map(function ($value) use ($count, $elements){
                return round(100 * $value / $count, 1);
            }, $elements);

            // Need to assign ElementItem instances
            $final = collect();
            foreach ($elements as $symbol => $percentage){
                $final->push(['element'=>ElementItem::find($symbol), 'percentage' => $percentage]);
            }
            // Finally
            $item->elements = $final;
            $item->durability = 1;
            $items->push($item);

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
class Item
{
    public int $id;
    public string $name;
    public mixed $description;
}
// Need custom logic for them I think
class MachineItem extends Item
{
    public string $type;
    public array $slots;
}
// Base resource element for crafts
class ElementItem extends Item
{
    public static function find($symbol)
    {
        return Resource::load()->firstWhere('symbol', $symbol);
    }
    public string $symbol;
    public mixed $color;
    public mixed $melt;
    public mixed $boil;
    public int $mass;
    public bool $metal;
    public bool $radioactive;
}
// Sum up all numbers and calc percentage of elements in mineral (ore).
class MineralItem extends Item
{
    public float $durability;
    public mixed $color;
    public mixed $elements;
}
// Only for start with Mine
class ToolItem extends Item
{
    public int $durability;
    public int $effectiveness;
}
// Items can be done out of every Metal
class Recipe
{
    public int $id;
    public array $ingredients;
    public Item $result;
}
