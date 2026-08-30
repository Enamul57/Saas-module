<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use App\traits\HasPermissionDirect;

class UserController extends Controller
{
    use HasPermissionDirect;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ✅ Use direct permission check
        if (!$this->hasPermissionDirect('view_users')) {
            abort(403, 'You do not have permission to view users.');
        }

        $users = User::with('roles')
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            })
            ->paginate(10);

        $users->getCollection()->transform(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray(),
                'role' => $user->roles->first()?->name,
                'is_active' => $user->is_active,
                'created_at' => $user->created_at,
            ];
        });

        return Inertia::render('UserManagement/User/Index', [
            'users' => $users,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Search users for linking to employees.
     */
    public function search(Request $request)
    {
        // ✅ Use direct permission check
        if (!$this->hasPermissionDirect('view_users')) {
            abort(403, 'You do not have permission to view users.');
        }

        try {
            $query = $request->get('q');

            if (empty($query) || strlen($query) < 2) {
                return response()->json([]);
            }

            $users = User::where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            })
                ->whereNotIn('id', function ($q) {
                    $q->select('user_id')
                        ->from('employees')
                        ->whereNotNull('user_id');
                })
                ->where('id', '!=', auth()->id())
                ->limit(10)
                ->get(['id', 'name', 'email']);

            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('Search error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ✅ Use direct permission check
        if (!$this->hasPermissionDirect('create_users')) {
            abort(403, 'You do not have permission to create users.');
        }

        return Inertia::render('UserManagement/User/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ✅ Use direct permission check
        if (!$this->hasPermissionDirect('create_users')) {
            abort(403, 'You do not have permission to create users.');
        }

        $data = $request->validate([
            'name' => 'required|string',
            'email' => ['required', 'email', Rule::unique('users')->where(fn($q) => $q->where('tenant_id', session('tenant_id')))],
            'password' => 'required|string|min:6',
        ]);

        $data['tenant_id'] = session('tenant_id');

        User::create($data);

        return to_route('admin.users.index')->with('success', 'User Created Successfully');
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        // ✅ Use direct permission check
        if (!$this->hasPermissionDirect('view_users')) {
            abort(403, 'You do not have permission to view users.');
        }

        $user = User::with('roles')->findOrFail($id);
        return Inertia::render('UserManagement/User/Show', [
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // ✅ Use direct permission check
        if (!$this->hasPermissionDirect('edit_users')) {
            abort(403, 'You do not have permission to edit users.');
        }

        $user = User::with('roles')->findOrFail($id);
        return Inertia::render('UserManagement/User/Edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // ✅ Use direct permission check
        if (!$this->hasPermissionDirect('edit_users')) {
            abort(403, 'You do not have permission to edit users.');
        }

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)->where(fn($q) => $q->where('tenant_id', session('tenant_id')))],
        ]);

        if ($request->filled('password')) {
            $validated['password'] = $request->password;
        }

        $user->update($validated);

        return to_route('admin.users.index')->with('info', 'User Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // ✅ Use direct permission check
        if (!$this->hasPermissionDirect('delete_users')) {
            abort(403, 'You do not have permission to delete users.');
        }

        // ✅ Prevent users from deleting themselves
        if ($id == auth()->id()) {
            return to_route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        $user = User::find($id);

        if (!$user) {
            return to_route('admin.users.index')->with('error', 'User not found.');
        }

        $user->delete();

        return to_route('admin.users.index')->with('danger', 'User Deleted Successfully');
    }
}