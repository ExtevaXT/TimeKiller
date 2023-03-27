<?php

use App\Resource\Items\Items\Items\Items\MineralItem;
use App\Resource\Items\Items\Items\Items\Resource;
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
    //Resource::make();
    dd(Resource::load()->get(1129)) ;
});
