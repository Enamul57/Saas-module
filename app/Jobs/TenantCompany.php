<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TenantCompany implements ShouldQueue
{
    use Queueable;
    protected $tenant;
    /**
     * Create a new job instance.
     */
    public function __construct(Tenant $tenant)
    {
        //
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $this->tenant->run(function () {
            $user = User::create([
                'name' => $this->tenant->company_name,
                'email' => $this->tenant->email,
                'password' => $this->tenant->password,
                'is_active' => true,
            ]);

            $user->assignRole('admin');
        });
    }
}
