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
            if ($data['showCredentials']) {
                $user = User::create([
                    'name' => $data['first_name'] . " " . $data['middle_name'] . " " . $data['last_name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'remember_token' => Str::random(10),
                    'tenant_id' => session('tenant_id'),
                ]);
                $employee->user_id = $user->id;
                Log::info('User Created Successfully.', $user->toArray());
                //create Role employee or Update
                $roleName = 'employee';
                //logging tenant id
                Log::info('employee create tenant id: ', ['tenant_id' => session('tenant_id')]);
                $role = TenantRole::firstOrCreate(['name' => strtolower($roleName)]);
                $user->roles()->syncWithoutDetaching([
                    $role->id => ['tenant_id' => session('tenant_id')]
                ]);
            }
            Log::info('employee: ', ['employee' => $employee->toArray()]);
            $employee->save();

            return $employee;
        } catch (\Exception $e) {
            Log::error('Error creating employee: ' . $e->getMessage());
            throw $e; // Rethrow the exception after logging it
        }
    }
}
