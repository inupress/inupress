<?php

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
    return view('welcome');
});

/*
 * Fix: Route [login] not defined error
 */
foreach (filament()->getPanels() as $panel) {
    Route::redirect('/login', $panel->getPath() . '/login')->name('login');
}
