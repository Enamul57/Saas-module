<?php

use Illuminate\Support\Facades\Route;
use Modules\PIM\Http\Controllers\PIMController;

Route::middleware(['auth', 'verified'])->prefix('pims')->name('pim.')->group(function () {

    // Main employee list (index)
    Route::get('/', [PIMController::class, 'index'])
        ->middleware('permission:view_employees')
        ->name('index');

    // Employee list view (alternative)
    Route::get('/employee-list', [PIMController::class, 'employeeList'])
        ->middleware('permission:view_employees')
        ->name('EmployeeList');

    // Create employee
    Route::get('/create', [PIMController::class, 'create'])
        ->middleware('permission:create_employee')
        ->name('create');
    Route::delete('/employee/{employee}', [PIMController::class, 'destroy'])
        ->middleware('permission:delete_employee')
        ->name('deleteEmployee');
    // Store employee
    Route::post('/add-employee', [PIMController::class, 'store'])
        ->middleware('permission:create_employee')
        ->name('storeEmployee');

    // Update employee
    Route::post('/update-employee/{employee}', [PIMController::class, 'update'])
        ->middleware('permission:edit_employee')
        ->name('updateEmployee');

    // Delete employee
    Route::delete('/employee/{employee}', [PIMController::class, 'destroy'])
        ->middleware('permission:delete_employee')
        ->name('deleteEmployee');

    // ==================== EMPLOYEE DETAILS ====================
    // View employee details
    Route::get('/employee/{employee}/personal-details', [PIMController::class, 'personal_details'])
        ->middleware('permission:view_employee_details')
        ->name('getPersonalDetails');

    // Store personal details
    Route::post('/employee/{employee}/personal_details', [PIMController::class, 'storePersonalDetails'])
        ->middleware('permission:edit_employee')
        ->name('storePersonalDetails');

    // Contact details
    Route::get('/{employee}/contact-details', [PIMController::class, 'contact_details'])
        ->middleware('permission:view_employee_details')
        ->name('ContactDetails');

    Route::post('/employee/{employee}/contact-details', [PIMController::class, 'storeContactDetails'])
        ->middleware('permission:edit_employee')
        ->name('storeContactDetails');

    // Job details
    Route::get('/{employee}/job-details', [PIMController::class, 'job_details'])
        ->middleware('permission:view_job_details')
        ->name('JobDetails');

    Route::post('/employee/{employee}/job-details', [PIMController::class, 'storeJobDetails'])
        ->middleware('permission:edit_job')
        ->name('storeJobDetails');

    // Salary details
    Route::get('/{employee}/salary-details', [PIMController::class, 'salary_details'])
        ->middleware('permission:view_employee_salary')
        ->name('SalaryDetails');

    // ==================== REPORTS ====================
    Route::get('/reports', [PIMController::class, 'reports'])
        ->middleware('permission:view_reports')
        ->name('Reports');
});
