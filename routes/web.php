<?php

use App\Http\Controllers\aktiviteController;
use App\Http\Controllers\siteController;
use App\Http\Controllers\panelController;
use App\Http\Controllers\panelloginController;
use App\Http\Middleware\adminMiddleware;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

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


Route::get('/', [siteController::class, 'home'])->name('anasayfa');
Route::get('/kurumsal', [siteController::class, 'kurumsal'])->name('kurumsal');
Route::get('/aktiviteler', [siteController::class, 'aktiviteler'])->name('aktiviteler');
Route::get('/galeri', [siteController::class, 'galeri'])->name('galeri');
Route::get('/iletisim', [siteController::class, 'iletisim'])->name('iletisim');
Route::post('/registration-send', [siteController::class, 'sendRegistrationForm'])->name('registration-send');
Route::post('/contact-send', [siteController::class, 'sendContactForm'])->name('contact-send');


Route::get('/panel', [panelloginController::class, 'inOrOut']);
Route::post('/panel/login', [panelloginController::class, 'login']);

Route::prefix('panel')->middleware([adminMiddleware::class])->group(function(){        
    Route::get('settings', [panelController::class, 'settings'])->name('settings');
    Route::put('settings', [panelController::class, 'updateSettings'])->name('updateSettings');
    Route::resource('aktivite', aktiviteController::class);
    // Route::post('project/change-order',[panelController::class, 'changeOrder']);
    Route::get('logout', [panelloginController::class, 'logout']);

 });