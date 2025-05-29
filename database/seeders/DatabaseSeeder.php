<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Order;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProductSeeder::class,
            RoleSeeder::class,
        ]);
        $users = User::factory()->count(10)->create();
        $users->each(function ($user) {
            Order::factory()
                ->count(rand(1, 3))
                ->for($user)
                ->create();
        });
    }
}
