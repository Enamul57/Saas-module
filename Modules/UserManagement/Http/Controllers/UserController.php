<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $users = User::whereNot('id', auth()->id())
            ->orderBy('id', 'desc')
            ->paginate($perPage) // Laravel pagination
            ->withQueryString(); // keep search/filter query

        return Inertia::render('UserManagement/User/Index', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('usermanagement::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        $data['tenant_id'] = session('tenant_id');
        // Create user in DB
        User::create($data);
        return to_route('users.index')->with('success', 'User Created Successfully');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('usermanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('usermanagement::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);
        if ($request->filled('password')) {
            $validated['password'] = $request->password;
        }
        $user->update($validated);
        return to_route('users.index')->with('info', 'User Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return to_route('users.index')->with('danger', 'User Deleted Successfully');
    }
}
