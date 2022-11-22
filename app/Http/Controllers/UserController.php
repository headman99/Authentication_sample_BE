<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuRecipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function getMenuCatalog(Request $request)
    {
        try {
            $request->validate([
                "menu_id" => "integer"
            ]);
            $menu = Menu::find($request->menu_id);

            if (!$menu) {
                return response(null, \Illuminate\Http\Response::HTTP_NO_CONTENT);
            }

            $menuRecipes = MenuRecipe::where("menu_id", $request->menu_id)
                ->join("products", "menu_recipes.product_id", "=", 'products.id')
                ->select("products.id", "products.nome", "menu_recipes.gruppo", "menu_recipes.sezione", "menu_recipes.extra")
                ->get();

            if (empty($menuRecipes)) {
                return response(null, \Illuminate\Http\Response::HTTP_NO_CONTENT);
            }


            return response([
                "menu" => $menu,
                "products" => $menuRecipes
            ]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function getMenuDetails()
    {
        try {
            $menu = Menu::all();
            if (empty($menu)) {
                return response(null, \Illuminate\Http\Response::HTTP_NO_CONTENT);
            }
            return response(["menus" => $menu]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }
}
