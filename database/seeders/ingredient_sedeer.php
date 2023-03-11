<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ingredient_sedeer extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ingr = new Ingredient(["name" => 'Pane al latte',"team" => 1]);$ingr->save();
        $ingr = new Ingredient(["name" => 'Pomodorino giallo',"team" => 1]);$ingr->save();
        $ingr = new Ingredient(["name" => 'Mascarpone',"team" => 1]);$ingr->save();
        $ingr = new Ingredient(["name" => 'Uova',"team" => 2]);$ingr->save();
        $ingr = new Ingredient(["name" => 'Patate',"team" => 2]);$ingr->save();
        $ingr = new Ingredient(["name" => 'Provola',"team" => 2]);$ingr->save();
        $ingr = new Ingredient(["name" => 'Pepe',"team" => 2]);$ingr->save();
    }
}
