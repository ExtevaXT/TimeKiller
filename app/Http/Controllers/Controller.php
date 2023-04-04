<?php

namespace App\Http\Controllers;

use App\Resource\Items\Instance;
use App\Resource\Items\Instances\Machine;
use App\Resource\Items\Instances\Storage;
use App\Resource\Items\Mineral;
use App\Resource\Resource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function plot()
    {
        $instances = Auth::user()->instances;
        $plot = collect(array_fill(1, 50, [
            'slot' => 0,
            'instance' => '',
        ]));
        foreach ($instances as $row){
            $plot->put($row->slot, $row);
        }
        return view('plot', ['plot'=>$plot]);
    }
    public function build()
    {
        $instance = Resource::findBy('name','Lithium Generator');
        Auth::user()->instances()->create(['instance'=> $instance->name, 'slot'=>5]);
    }

    public function inventory()
    {

    }
    public function instance(Request $request)
    {
        $slot = $request->slot;
        $instance = Auth::user()->instances->where('slot', $slot)->first();

        $slots = collect(array_fill(1, 50, null));
        foreach ($instance->slots as $row){
            $slots->put($row->slot, $row);
        }
        if($instance->item() instanceof Storage)
            return view('storage', compact('instance','slots'));
        if($instance->item() instanceof Machine)
            return view('machine', compact('instance'));
        return back();
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
    public function craft()
    {
        return view('craft');
    }
}
