<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return Inertia::render('create_tenant');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'company_name' => 'required|string|max:255|unique:tenants',
            'email'        => 'required|email|unique:tenants,email',
            'password'     => 'required|string|min:8|confirmed',
        ]);

        $company = Tenant::create([
            'company_name' => $validated['company_name'],
            'email'        => $validated['email'],
            'password'     => $validated['password'],
        ]);

        $company->domains()->create([
            'domain' => $request->domain . "." . config('app.domain'),
        ]);
        return redirect()->route('central.dashboard')
            ->with('status', 'Company created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
