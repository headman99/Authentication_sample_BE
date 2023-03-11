<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuRecipeResource;
use App\Models\Menu;
use App\Models\MenuRecipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                ->select("products.id as id", "products.nome as nome", "menu_recipes.gruppo as gruppo", "menu_recipes.sezione as sezione", "menu_recipes.alternative as alternative","menu_recipes.ratio as ratio", "menu_recipes.groupPosition as groupPosition")
                ->orderBy("menu_recipes.groupPosition")
                ->get();

            if (empty($menuRecipes)) {
                return response(null, \Illuminate\Http\Response::HTTP_NO_CONTENT);
            }


            return response([
                "menu" => $menu,
                "products" => MenuRecipeResource::collection($menuRecipes)
            ]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova', "exc" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function getMenuDetails()
    {
    
        try {
            if(Auth::user()->isadmin==1)
                $menu = Menu::all();
            else
                $menu = Menu::where("active",true)->get();
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
