<?php

namespace App\Http\Controllers;

use App\Resource\Items\Mineral;
use App\Resource\Resource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function plot()
    {

    }
    public function inventory()
    {

    }
    public function machine()
    {

    }
    public function mine($seed = null)
    {
        if(!$seed) $seed = rand(1, 999999999);
        $ore_chance = 5;
        $vein_horizontal_chance = 30;
        $vein_vertical_chance = 50;
        $mine = collect();
        for($h = 0; $h < 255; $h++){
            $line = collect();
            $ore = null;
            if(rand(0, 99) < $ore_chance)
                $ore = Resource::load()
                    ->whereInstanceOf(Mineral::class)
                    ->random();
            for($w = 0; $w < 10; $w++)
                if(rand(0, 99) < $vein_horizontal_chance) {
                    $line->push($ore);
                    if(rand(0,99) < $vein_vertical_chance){
                        $direction = rand(0,1) ? 1 : -1;
                        $_line = $mine->get($h + $direction);
                        if($_line)
                            foreach ($_line as $_ore){
                                if(rand(0,99) < $vein_vertical_chance){
                                    $_line->put($w, $ore);
                                    $mine->put($h + $direction, $_line);
                                }
                            }
                    }
                }
                else {
                    $line->push(null);
                }
            $mine->push($line);
        }
        return view('mine', ['mine'=>$mine, 'seed'=>$seed]);
        //dd($mine);
    }

    public function collect()
    {

    }

    public function build()
    {

    }

    public function craft()
    {
        return view('craft');
    }
}
