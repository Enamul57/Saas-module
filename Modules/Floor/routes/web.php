<?php

use Illuminate\Support\Facades\Route;
use Modules\Floor\Http\Controllers\FloorController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('floors', FloorController::class)->names('floor');
});
