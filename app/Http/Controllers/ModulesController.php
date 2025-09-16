<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Modules;

class ModulesController extends Controller
{
    //
    public function create()
    {
        $modules = Modules::all();
        return Inertia::render('UserManagement/User/Permission', [
            'modules' => $modules
        ]);
    }
}
