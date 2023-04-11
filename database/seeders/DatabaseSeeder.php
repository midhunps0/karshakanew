<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DistrictSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        // $this->call(TalukSeeder::class);
        // $this->call(VillageSeeder::class);
        $this->call(TradeUnionSeeder::class);
        $this->call(ReligionSeeder::class);
        $this->call(CasteSeeder::class);
        $this->call(FeeTypeSeeder::class);
        // $this->call(UserSeeder::class);
        $this->call(MemberSeeder::class);
        $this->call(PaymentModeSeeder::class);
        $this->call(FeeCollectionSeeder::class);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
