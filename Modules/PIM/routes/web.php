<?php

use Illuminate\Support\Facades\Route;
use Modules\PIM\Http\Controllers\PIMController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('pims', PIMController::class)->names('pim');
    Route::post('/pim/add-employee', [PimController::class, 'store'])->name('PIM.storeEmployee');
    Route::post('/pim/update-employee', [PimController::class, 'update'])->name('PIM.updateEmployee');
    Route::get('/pim/employee-list', [PimController::class, 'employeeList'])->name('PIM.EmployeeList');
    Route::get('/pim/reports', [PimController::class, 'reports'])->name('PIM.Reports');
});
