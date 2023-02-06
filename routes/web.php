<?php

use App\Http\Controllers\GoogleMapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GoogleMapController::class, 'showMap'])->name('map');
Route::get('/search', [GoogleMapController::class, 'searchPlace'])->name('search');
