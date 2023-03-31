<?php

use App\Http\Controllers\Controller;
use App\Resource\Resource;
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
    dd(Resource::load()->where('material','Lithium')->take(30));
});
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
