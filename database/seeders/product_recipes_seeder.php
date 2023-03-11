<?php

namespace Database\Seeders;

use App\Models\ProductReceips;
use App\Models\Stoks;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class product_recipes_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pr = new ProductReceips(['product_id'=>12,'ingredient_id'=>1,'quantity'=>40]);$pr->save();
        $pr = new ProductReceips(['product_id'=>12,'ingredient_id'=>2,'quantity'=>20]);$pr->save();
        $pr = new ProductReceips(['product_id'=>12,'ingredient_id'=>3,'quantity'=>60]);$pr->save();
        $pr = new ProductReceips(['product_id'=>210,'ingredient_id'=>4,'quantity'=>15]);$pr->save();
        $pr = new ProductReceips(['product_id'=>210,'ingredient_id'=>5,'quantity'=>100]);$pr->save();
        $pr = new ProductReceips(['product_id'=>210,'ingredient_id'=>6,'quantity'=>50]);$pr->save();
        $pr = new ProductReceips(['product_id'=>210,'ingredient_id'=>7,'quantity'=>20]);$pr->save();
        
    }
}
