<?php

namespace Database\Seeders;

use App\Models\ProductGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class products_groups_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pg = new ProductGroup(["gruppo"=>"DRINK"]); $pg->save();
        $pg = new ProductGroup(["gruppo"=>"PASTA"]); $pg->save();
        $pg = new ProductGroup(["gruppo"=>"PESCE"]); $pg->save();
        $pg = new ProductGroup(["gruppo"=>"SALUMI"]); $pg->save();
        $pg = new ProductGroup(["gruppo"=>"FORMAGGI"]); $pg->save();
        $pg = new ProductGroup(["gruppo"=>"ORTAGGI"]); $pg->save();
        $pg = new ProductGroup(["gruppo"=>"FARINACEI"]); $pg->save();
        $pg = new ProductGroup(["gruppo"=>"VINI"]); $pg->save();
        $pg = new ProductGroup(["gruppo"=>"DOLCI"]); $pg->save();
        $pg = new ProductGroup(["gruppo"=>"CARNE"]); $pg->save();
        $pg = new ProductGroup(["gruppo"=>"FRUTTA"]); $pg->save();
        $pg = new ProductGroup(["gruppo"=>"OPEN BAR"]); $pg->save();
        $pg = new ProductGroup(["gruppo"=>"ZUPPE"]); $pg->save();
        $pg = new ProductGroup(["gruppo"=>"BAMBINI"]); $pg->save();
    }
}
