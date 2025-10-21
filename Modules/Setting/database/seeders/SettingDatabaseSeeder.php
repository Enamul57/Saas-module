<?php

namespace Modules\Setting\Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Modules\Setting\Database\Seeders\SettingSeeder;

class SettingDatabaseSeeder  extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
        ]);
    }
}
