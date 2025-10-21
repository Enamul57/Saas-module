<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\App\Models\Setting;
use Modules\Setting\Http\Controllers\SettingController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('settings', SettingController::class);
    Route::get('setting/json', [SettingController::class, 'getSettingJson'])->name('setting.json');
});
