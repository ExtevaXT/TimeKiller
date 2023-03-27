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
    dd(Resource::load()->get(1129));
});
Route::get('/make', function () {
    Resource::make();
    Resource::make(true);
});
Route::get('/mine', [Controller::class, 'mine']);
