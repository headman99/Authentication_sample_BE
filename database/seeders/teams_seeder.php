<?php

namespace Database\Seeders;

use App\Models\ProductGroup;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class teams_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $team = new Team(["name"=>"Team Verdura"]);$team->save();
        $team = new Team(["name"=>"Team Pasta"]);$team->save();
    }
}
