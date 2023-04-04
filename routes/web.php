<?php

use App\Http\Controllers\Controller;
use App\Resource\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    dd(Auth::user()->freeSlots());
    //dd(Resource::load()->whereInstanceOf(\App\Resource\Items\Instances\Storage::class)->take(30));
});
Route::get('/login', function (\Illuminate\Http\Request $request) {
    if(Auth::attempt(['name'=>1,'password'=>1])){
        $request->session()->regenerate();
        return 'ok';
    }
    return 'not ok';
});
Route::get('/add', function (\Illuminate\Http\Request $request) {
    Auth::user()->addItem(rand(0, 5000));
});

Route::get('/instance', [Controller::class, 'instance']);

Route::get('/plot', [Controller::class, 'plot']);
Route::get('/build', [Controller::class, 'build']);
Route::get('/make', function () {
    ini_set('max_execution_time', 500);
    Resource::make(false, true);
    //Resource::make(true, true);
    dd(Resource::load()->count());
});
Route::get('/mine', [Controller::class, 'mine']);
//Route::get('/mine', \App\Http\Livewire\Mine::class);
Route::get('/craft', [Controller::class, 'craft']);
Route::get('/resource', function () {
   return view('resources');
});
Route::get('/test', function () {
    dd(json_decode(file_get_contents(resource_path('data/template/instance/generators.json')), true));
    //dd(new \App\Resource\Items\Instances\Machines\Generator());
});
