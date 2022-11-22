<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class menu_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menu = new Menu(["nome"=>"FEELINGOOD"]);$menu->save();
        $menu = new Menu(["nome"=>"LIKECHR"]);$menu->save();
        $menu = new Menu(["nome"=>"NATURE CHIC"]);$menu->save();
        $menu = new Menu(["nome"=>"PROPOSTA 1","descrizione" =>"Antipasti, Tapas a braccio"]);$menu->save();
        $menu = new Menu(["nome"=>"PROPOSTA NATALE 1","descrizione" =>"pubblica - Villa Samuel"]);$menu->save();
        $menu = new Menu(["nome"=>"PROPOSTA NATALE 2","descrizione" =>"pubblica con antipasti serviti"]);$menu->save();
        $menu = new Menu(["nome"=>"PROPOSTA 3","descrizione"=>"pubblica menù antipasti serviti, frutta e dolci a buffet NEW 2022"]);$menu->save();
        $menu = new Menu(["nome"=>"PROPOSTA 4","descrizione" =>"pubblica menù con BUFFET 2022 - VILLA SAMUE"]);$menu->save();
        $menu = new Menu(["nome"=>"PROPOSTA 5","descrizione"=>"pubblica menù con BUFFET 2022"]);$menu->save();
    }
}
