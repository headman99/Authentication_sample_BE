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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(products_groups_seeder::class);
        $this->call(menu_seeder::class);
        $this->call(teams_seeder::class);
        $this->call(products_table_seeder::class);
        $this->call(menu_receips_seeder::class);
        $this->call(admin_seeder::class);
        $this->call(ingredient_sedeer::class);
        $this->call(stock::class);
        $this->call(product_recipes_seeder::class);
    }
}
