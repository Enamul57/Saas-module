<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Modules\Setting\App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Setting/Settings/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('setting::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $settings = $request->input('settings', []);
        foreach ($settings as $setting) {
            Setting::updateOrCreate([
                'id' => $setting['id']
            ], [
                'key' => $setting['key'],
                'value' => $setting['value'],
            ]);
        }
        Log::info('Settings Update Request: ' . print_r($settings, true));
        return to_route('settings.index');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('setting::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('setting::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    public function getSettingJson()
    {
        $settings = Setting::all();
        return response()->json($settings);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
