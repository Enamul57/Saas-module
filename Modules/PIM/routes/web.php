<?php

use Illuminate\Support\Facades\Route;
use Modules\PIM\Http\Controllers\PIMController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('pims', PIMController::class)->names('pim');
    Route::post('/pim/add-employee', [PIMController::class, 'store'])->name('PIM.storeEmployee');
    Route::post('/pim/update-employee', [PIMController::class, 'update'])->name('PIM.updateEmployee');

    // Employee list and reports
    Route::get('/pim/employee-list', [PIMController::class, 'employeeList'])->name('PIM.EmployeeList');
    Route::get('/pim/reports', [PIMController::class, 'reports'])->name('PIM.Reports');

    // Employee details routes
    Route::get('/pim/employee/{employee}/personal-details', [PIMController::class, 'personal_details'])->name('PIM.getPersonalDetails');
    Route::post('/pim/employee/{employee}/personal_details', [PIMController::class, 'storePersonalDetails'])->name('PIM.storePersonalDetails');

    Route::get('/pim/{employee}/contact-details', [PIMController::class, 'contact_details'])->name('PIM.ContactDetails');
    Route::post('/pim/employee/{employee}/contact-details', [PIMController::class, 'storeContactDetails'])->name('PIM.storeContactDetails');

    Route::get('/pim/{employee}/job-details', [PIMController::class, 'job_details'])->name('PIM.JobDetails');
    Route::post('/pim/employee/{employee}/job-details', [PIMController::class, 'storeJobDetails'])->name('PIM.storeJobDetails');

    Route::get('/pim/{employee}/salary-details', [PIMController::class, 'salary_details'])->name('PIM.SalaryDetails');

    // Delete employee
    Route::delete('/pim/employee/{employee}', [PIMController::class, 'destroy'])->name('PIM.deleteEmployee');
});
