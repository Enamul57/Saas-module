<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Modules\PIM\Http\Controllers\PIMController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('pims', PIMController::class)->names('pim');
    Route::post('/pim/add-employee', [PIMController::class, 'store'])->name('PIM.storeEmployee');
    Route::post('/pim/update-employee', [PIMController::class, 'update'])->name('PIM.updateEmployee');
    Route::get('/pim/employee-list', [PIMController::class, 'employeeList'])->name('PIM.EmployeeList');
    Route::get('/pim/reports', [PIMController::class, 'reports'])->name('PIM.Reports');
    Route::get('/pim/emplooyee/{employee}/personal-details', [PIMController::class, 'personal_details'])->name('PIM.getPersonalDetails');
    Route::post('/pim/employee/{employee}/personal_details', [PIMController::class, 'storePersonalDetails'])->name('PIM.storePersonalDetails');
    Route::get('/reverse-geocode', function () {


        $lat = 23.7747523;
        $lon = 90.365421;
        $cacheKey = "reverse_geocode:{$lat}:{$lon}";

        // cache for 12 hours
        $result = Cache::remember($cacheKey, now()->addHours(12), function () use ($lat, $lon) {
            $response = Http::withHeaders([
                'User-Agent' => 'MyLaravelApp/1.0 (your-email@example.com)',
            ])->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'jsonv2',
                'lat' => $lat,
                'lon' => $lon,
                'addressdetails' => 1,
            ]);

            return $response->successful() ? $response->json() : null;
        });

        if (! $result) {
            return response()->json(['message' => 'Reverse geocoding failed'], 502);
        }

        return response()->json([
            'display_name' => $result['display_name'] ?? null,
            'address' => $result['address'] ?? [],
            'raw' => $result,
                
        ]);
    });
});
