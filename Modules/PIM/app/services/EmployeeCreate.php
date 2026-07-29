<?php

namespace Modules\PIM\App\Services;

use App\Models\TenantRole;
use App\Models\User;
use Modules\PIM\App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmployeeCreate
{
    public function createEmployee(array $data): Employee
    {
        try {
            Log::info('employee', ['data' => $data]);

            $employee = new Employee();
            $employee->first_name = $data['first_name'];
            $employee->middle_name = $data['middle_name'] ?? null;
            $employee->last_name = $data['last_name'];
            $employee->employee_id = $data['employee_id'];
            $employee->email = $data['email'] ?? null;

            if (isset($data['img'])) {
                $employee->img = storeFile($data['img'], 'employees');
            }

            Log::info('creating: ', ['employee' => $employee->toArray()]);

            // ✅ Check if we should link to an existing user
            if (isset($data['link_user_id']) && !empty($data['link_user_id'])) {
                // Link to existing user
                $user = User::find($data['link_user_id']);
                if ($user) {
                    $employee->user_id = $user->id;
                    Log::info('Linked to existing user', ['user_id' => $user->id, 'email' => $user->email]);

                    // ✅ Ensure user has employee role
                    $this->assignEmployeeRole($user);
                } else {
                    Log::error('User not found for linking', ['user_id' => $data['link_user_id']]);
                }
            }
            // ✅ Create new user if credentials are provided
            elseif (isset($data['showCredentials']) && $data['showCredentials']) {
                $user = User::create([
                    'name' => trim($data['first_name'] . " " . ($data['middle_name'] ?? '') . " " . $data['last_name']),
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'remember_token' => Str::random(10),
                    'tenant_id' => session('tenant_id'),
                ]);
                $employee->user_id = $user->id;
                Log::info('User Created Successfully.', $user->toArray());

                // ✅ Assign employee role
                $this->assignEmployeeRole($user);
            }
            // ✅ If no credentials and no link, create user without login (optional)
            // else {
            //     // Create user with default password
            //     $user = User::create([
            //         'name' => trim($data['first_name'] . " " . ($data['middle_name'] ?? '') . " " . $data['last_name']),
            //         'email' => $data['email'],
            //         'password' => Hash::make('password123'),
            //         'remember_token' => Str::random(10),
            //         'tenant_id' => session('tenant_id'),
            //     ]);
            //     $employee->user_id = $user->id;
            //     $this->assignEmployeeRole($user);
            //     Log::info('User Created with default password.', $user->toArray());
            // }

            Log::info('employee: ', ['employee' => $employee->toArray()]);
            $employee->save();

            return $employee;
        } catch (\Exception $e) {
            Log::error('Error creating employee: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Assign employee role to user
     */
    private function assignEmployeeRole(User $user): void
    {
        $roleName = 'employee';
        Log::info('Assigning employee role', ['tenant_id' => session('tenant_id'), 'user_id' => $user->id]);

        $role = TenantRole::firstOrCreate([
            'name' => strtolower($roleName),
            'tenant_id' => session('tenant_id'),
        ]);

        $user->roles()->syncWithoutDetaching([
            $role->id => ['tenant_id' => session('tenant_id')]
        ]);

        // Update the role column in users table
        $user->update(['role' => 'employee']);

        Log::info('Employee role assigned', ['user_id' => $user->id, 'role_id' => $role->id]);
    }

    public function updateEmployee(Employee $employee, array $data): Employee
    {
        $employee->first_name = $data['first_name'];
        $employee->middle_name = $data['middle_name'] ?? null;
        $employee->last_name = $data['last_name'];

        if (isset($data['img']) && $data['img']) {
            // Delete old file
            if ($employee->img) {
                deleteFile($employee->img);
            }
            // Store new file
            $employee->img = storeFile($data['img'], 'employees');
        }

        $employee->save();
        return $employee;
    }
}
