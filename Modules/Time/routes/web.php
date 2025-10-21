<?php

use Illuminate\Support\Facades\Route;
use Modules\Time\Http\Controllers\TimeController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('times', TimeController::class)->names('time');
});
