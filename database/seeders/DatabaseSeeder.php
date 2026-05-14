<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Usertype;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
   
Usertype::factory()->create([
            'name' => 'Admin',
            'description' => 'Administrator with full access',
        ]);
        User::factory()->create([
            'first_name' => 'Faith',
            'last_name' => 'Akunna',
            'email' => 'olekamma.faith@gmail.com',
            'password' => bcrypt('faithfulGod'),
            'user_type_id' => 1,
            'is_admin' => true,

        ]);
    }
}
