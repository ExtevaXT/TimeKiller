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
    dd(Resource::load()->whereInstanceOf(\App\Resource\Items\Instance::class)->take(30));
});
Route::get('/make', function () {
    ini_set('max_execution_time', 500);
    Resource::make(false, true);
    //Resource::make(true, true);
    dd(Resource::load()->count());
});
Route::get('/mine', [Controller::class, 'mine']);
Route::get('/resource', function () {
    return view('resources');
});
