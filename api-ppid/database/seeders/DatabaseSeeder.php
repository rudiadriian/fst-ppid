<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModulSistemSeeder::class,
            // Menyusul ModulSistemSeeder: tahapnya menunjuk role yang dibuat
            // di sana, dan kotak bagan yang dibuat BaganStrukturPpidSeeder.
            BaganStrukturPpidSeeder::class,
            AlurApprovalSeeder::class,
        ]);
    }
}
