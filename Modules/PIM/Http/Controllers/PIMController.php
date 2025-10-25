<?php

namespace Modules\PIM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Modules\PIM\app\Http\Requests\EmployeeStore;
use Modules\PIM\app\services\EmployeeCreate;

class PIMController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $employeeCreate;

    public function __construct(EmployeeCreate $employeeCreate)
    {
        $this->employeeCreate = $employeeCreate;
    }
    public function index()
    {
        return Inertia::render('PIM/PIM/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pim::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeStore $request)
    {

        $employee = $this->employeeCreate->createEmployee($request->validated());
        return to_route('pim.index');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('pim::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('pim::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
