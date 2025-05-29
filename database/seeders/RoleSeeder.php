<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Administrator with full access'],
            ['name' => 'client', 'description' => 'Client with access to own orders'],
        ];
        foreach ($roles as $role) {
            DB::table('roles')->insert($role);
        }
    }
}
