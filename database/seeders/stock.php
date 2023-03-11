<?php

namespace Database\Seeders;

use App\Models\Stoks;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class stock extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $s = new Stoks(["ingredient_id" => 1,"quantity"=>200]);$s->save();
        $s = new Stoks(["ingredient_id" => 2,"quantity"=>200]);$s->save();
        $s = new Stoks(["ingredient_id" => 3,"quantity"=>200]);$s->save();
        $s = new Stoks(["ingredient_id" => 4,"quantity"=>200]);$s->save();
        $s = new Stoks(["ingredient_id" => 5,"quantity"=>200]);$s->save();
        $s = new Stoks(["ingredient_id" => 6,"quantity"=>200]);$s->save();
        $s = new Stoks(["ingredient_id" => 7,"quantity"=>200]);$s->save();
    }
}
