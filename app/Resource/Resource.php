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
use App\Resource\Items\Instances\Machines\Gatherer;
use Illuminate\Support\Str;

class Resource
{
    const TPS = 20;
    const ORE = ['Ore', 'Crushed Ore', 'Purified Crushed Ore'];

    public static function load()
    {
        return unserialize(file_get_contents(resource_path('data/items')));
    }
    public static function make(bool $elementsFilled = false, $json = false)
    {
        $items = collect();
        // Fill up Elements
        //TODO Why tf you need them as items?
        foreach (self::elements() as $element){
            $item = new Element([
                'name' =>$element->name,
                'description' =>$element->summary,
                'color' =>$element->{"cpk-hex"},
                'symbol' =>$element->symbol,
                'melt' =>$element->melt,
                'boil' =>$element->boil,
                'mass' =>$element->atomic_mass,
                'radioactive' =>$element->atomic_mass > 207 or in_array($element->atomic_mass, [98, 145]),
                'metal' =>Str::contains($element->category, [' metal', 'lanthanide', 'actinide']),
            ]);
            $items->push($item);
            // Fill up Items made of this Element
            foreach (json_decode(file_get_contents(resource_path('data/template/types.json')), true) as $type){
                $component = new Component(['name'=> $element->name.' '.$type]);
                $component->material = $item->name;
                $component->type = $type;
                $items->push($component);
            }
            if($item->metal) {
                self::loadInstances('data/template/instance/generators.json', Generator::class, $element->name, $items);
                self::loadInstances('data/template/instance/processors.json', Processor::class, $element->name, $items);
                self::loadInstances('data/template/instance/gatherers.json', Gatherer::class, $element->name, $items);
                self::loadInstances('data/template/instance/storages.json', Storage::class, $element->name, $items);
                self::loadInstances('data/template/instance/energy_storages.json', EnergyStorage::class, $element->name, $items);
            }


        }
        // Fill up Minerals and their forms
        foreach (self::minerals() as $mineral) {
            if(!isset($mineral->formula)
                or Str::contains($mineral->formula, 'g/mol')
                or (float)Str::replace(['<sub>','</sub>'], '', $mineral->formula)
            ) continue;
            $item = new Mineral([
                'name' => $mineral->name,
                'description'=>$mineral->formula
            ]);
            $item->color = $mineral->color ?? null;
            $elements = self::formula($mineral->formula);
            // Count percentage
            $count = array_sum($elements);
            // Need to assign ElementItem instances
            $elementItems = collect();
            if($elementsFilled)
                foreach ($elements as $symbol => $value)
                    $elementItems->push(['element'=>self::findBy('symbol',$symbol), 'percentage' => round(100 * $value / $count, 1)]);
            // Finally
            $item->elements = $elements;
            $item->elementItems = $elementItems;
            $item->hardness = 1;
            $items->push($item);
            foreach (self::ORE as $form){
                $item = new Item(['name' => $mineral->name.' '.$form]);
                $items->push($item);
            }
        }
        $items->map(fn($item, $i) => $item->id = $i + 1);
        if($json) file_put_contents(resource_path('data/items.json'), json_encode($items, JSON_PRETTY_PRINT));
        file_put_contents(resource_path('data/items'), serialize($items));
    }
    // Loaders
    public static function elements()
    {
        return collect(json_decode(file_get_contents(resource_path('data/PeriodicTable.json')))->elements);
    }
    public static function minerals()
    {
        return collect(json_decode(file_get_contents(resource_path('data/Minerals.json'))));
    }
    public static function recipes()
    {
        return collect(json_decode(file_get_contents(resource_path('data/template/recipes.json'))));
    }
    public static function loadInstances($filename, $class, $element, $items) {
        $data = json_decode(file_get_contents(resource_path($filename)), true);

        foreach ($data as $instance) {
            $instance = new $class($instance, ['name' => $element.' '.$instance['name'], 'material'=>$element]);
            $items->push($instance);
        }
    }
    // Helpers
    public static function find($id)
    {
        return Resource::load()->firstWhere('id', $id);
    }
    public static function findBy($property, $value)
    {
        return Resource::load()->firstWhere($property, $value);
    }
    public static function flatten($array): array
    {
        $return = [];
        array_walk_recursive($array, function ($b, $a) use (&$return) {
            $return[] = [$a => $b];
        });
        return $return;
    }
    public static function formula($formula)
    {
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
        return array_map(function ($value){
            if(is_array($value)) $value = array_sum($value);
            return $value;
        }, $elements);
        //[['K'=>2], ['U'=>2], ['O'=>2], ['As'=>4], ['O'=>4], ['H'=>2], ['O'=>1]]
        //['K'=>2, 'U'=>2, 'O'=>2, 'As'=>4, 'O'=>4, 'H'=>2, 'O'=>1]
        //['K'=>2, 'U'=>2,'O'=>7, 'As'=>4, 'H'=>2]
    }
}

