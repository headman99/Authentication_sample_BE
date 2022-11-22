<?php

namespace App\Http\Controllers;

use App\Http\Resources\IngredientResource;
use App\Http\Resources\MenuPaginateResource;
use App\Http\Resources\MenuRecipeResource;
use App\Http\Resources\OrderMenuResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductIntsanceResource;
use App\Http\Resources\ProductResource;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\MenuRecipe;
use App\Models\Order_menu;
use App\Models\Product;
use App\Models\ProductInstance;
use App\Models\ProductReceips;
use App\Models\Stoks;
use App\Models\User;
use Carbon\Carbon;
use Hamcrest\Arrays\IsArray;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PHPUnit\TextUI\XmlConfiguration\Groups;

class AdminController extends Controller
{
    public function registerIngredient(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|max:100|string',
            'description' => 'sometimes|nullable|string|max:250',
            'quantity' => 'required|integer',
            'category' => 'sometimes|nullable|string|max:20'
        ]);

        try {
            DB::beginTransaction();
            $checkValidity = Ingredient::where('name', $request->name)->first();
            if ($checkValidity)
                return response(['message' => 'Inserisci un nome diverso da quelli già presenti'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
            $ingredient = Ingredient::create($validate);
            Stoks::create([
                'ingredient_id' => $ingredient->id,
                'quantity' => $request->quantity
            ]);

            DB::commit();
            return response(new IngredientResource($ingredient));
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }


    public function removeIngredient(Request $request)
    {
        $validate = $request->validate([
            'id' => 'required|integer'
        ]);

        try {
            DB::beginTransaction();
            Ingredient::find($request->id)->delete();
            Stoks::where('ingredient_id', $request->id)->delete();
            DB::commit();
            return response(['state' => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    //Modifica la quantità dell'ingrediente incrementandola o diminuendola in base alla modalità scelta
    public function addIngredientQuantity(Request $request)
    {
        Validator::make($request->all(), [
            "data" => "required|array|min:1",
            'data.*.ingredient' => 'required',
            'data.*.quantity' => 'sometimes|nullable|integer',
            'data.*.mode' => "redquired|integer"
        ]);


        try {
            DB::beginTransaction();
            foreach ($request->data as $obj) {
                $ingredient = Ingredient::where('name', $obj['ingredient'])->first();
                $stoks = Stoks::where('ingredient_id', $ingredient->id)->first();
                if ($obj['mode'] == 1) {
                    Stoks::where('ingredient_id', $ingredient->id)->update(['quantity' => ($obj['quantity'] + $stoks->quantity)]);
                }
                if ($obj['mode'] == 2) {
                    Stoks::where('ingredient_id', $ingredient->id)->update(['quantity' => $obj['quantity'] > $stoks->quantity ? 0 : ($stoks->quantity - $obj['quantity'])]);
                }
            }
            DB::commit();
            return response(['state' => 1], \Illuminate\Http\Response::HTTP_OK);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    //modifica la quantità relativa all'ingrediente sostituendola con la nuova
    public function updateIngredientQuantity(Request $request)
    {
        Validator::make($request->all(), [
            "data" => "required|array|min:1",
            'data.*.ingredient' => 'required',
            'data.*.quantity' => 'sometimes|nullable|integer',
        ]);


        try {
            DB::beginTransaction();
            foreach ($request->data as $obj) {
                //$ingredient = Ingredient::where('name', $obj['ingredient'])->first();
                Stoks::where('ingredient_id', $obj['ingredient'])->update(['quantity' => $obj['quantity']]);
            }
            DB::commit();
            return response(['state' => 1], \Illuminate\Http\Response::HTTP_OK);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }


    public function updateIngredientDescription(Request $request)
    {
        $request->validate([
            "data" => "required|array|min:1",
            'data.*.ingredient' => 'required',
            'data.*.description' => 'sometimes|nullable|string|max:250'
        ]);


        try {
            DB::beginTransaction();
            foreach ($request->data as $obj) {
                $ingredient = Ingredient::where('name', $obj['ingredient'])->first();
                $ingredient->update(['description' => $obj['description']]);
            }

            DB::commit();
            return response(['state' => 1], \Illuminate\Http\Response::HTTP_OK);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function getProductsCatalog()
    {
        $catalog = Product::select('id', 'nome', 'categoria', 'gruppo', 'descrizione')->orderBy("nome")->get();
        return $catalog;
    }

    public function getStock()
    {
        $stoks = DB::table('stoks')
            ->join('ingredients', 'ingredient_id', '=', 'ingredients.id')
            ->select('ingredient_id as id', 'ingredients.name', 'stoks.quantity', 'ingredients.description', 'ingredients.category', 'stoks.updated_at')
            ->orderBy('ingredients.name')
            ->get();
        return $stoks;
    }

    public function removeProduct(Request $request)
    {
        $request->validate([
            "id" => "required|integer"
        ]);
        try {
            Product::find($request->id)->delete();
            return response(["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function registerProduct(Request $request)
    {
        $validate = $request->validate([
            'nome' => 'required|max:100|string',
            'descrizione' => 'nullable|sometimes|string|max:250',
            'peso' => 'sometimes|integer',
            'categoria' => 'nullable|sometimes|string|max:10',
            'gruppo' => "nullable|string|sometimes"
        ]);

        try {
            DB::beginTransaction();
            $checkValidity = Product::where('nome', $request->nome)->first();
            if ($checkValidity)
                return response(['message' => 'Inserisci un nome diverso da quelli già presenti'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
            $product = Product::create($validate);
            DB::commit();
            return response([new ProductResource($product)]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function getProductGroups()
    {
        try {
            $groups = DB::table('products_groups')->select('gruppo')->get();
            return $groups;
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function addMenuRecipe(Request $request)
    {
        $validate = $request->validate([
            "product_id" => "required|integer",
            "menu_id" => "required|integer",
            "gruppo" => "required|string",
            "sezione" => "sometimes|nullable|string"
        ]);

        try {

            MenuRecipe::create([
                "product_id" => $request->product_id,
                "menu_id" => $request->menu_id,
                "gruppo" => $request->gruppo,
                "sezione" => $request->sezione === 'vuoto' ? '' : $request->sezione,
            ]);
            return response(["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function removeMenuRecipe(Request $request)
    {
        $validate = $request->validate([
            "product_id" => "required|integer",
            "menu_id" => "required",
            "gruppo" => "required|string",
            "sezione" => "sometimes|nullable|string"
        ]);
        try {
            MenuRecipe::where([
                ["product_id", $request->product_id],
                ["menu_id", '=', $request->menu_id],
                ["gruppo", '=', $request->gruppo],
                ["sezione", $request->sezione]
            ])->delete();
            return response(["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function removeMenuRecipeSection(Request $request)
    {
        $validate = $request->validate([
            "menu_id" => "required",
            "gruppo" => "required|string",
            "sezione" => "sometimes|nullable|string"
        ]);
        try {
            MenuRecipe::where([
                ["menu_id", '=', $request->menu_id],
                ["gruppo", '=', $request->gruppo],
                ["sezione", $request->sezione]
            ])->delete();
            return response(["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function removeMenuRecipeGroup(Request $request)
    {
        $validate = $request->validate([
            "menu_id" => "required|integer",
            "gruppo" => "required|string",
        ]);
        try {
            MenuRecipe::where([
                ["menu_id", $request->menu_id],
                ["gruppo", $request->gruppo],
            ])->delete();
            return response(["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }


    public function updateMenuRecipeGroup(Request $request)
    {
        $validate = $request->validate([
            "menu_id" => "required|integer",
            "old_gruppo" => "required|string",
            "new_gruppo" => "required|string|max:100"
        ]);
        try {
            MenuRecipe::where([
                ["menu_id", $request->menu_id],
                ["gruppo", $request->old_gruppo]
            ])->update(["gruppo" => $request->new_gruppo]);

            return response(["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateMenuRecipeSection(Request $request)
    {
        $validate = $request->validate([
            "menu_id" => "required|integer",
            "gruppo" => "required|string",
            "old_sezione" => "sometimes|nullable|string",
            "new_sezione" => "sometimes|nullable|string|max:100"
        ]);

        try {
            MenuRecipe::where([
                ["menu_id", $request->menu_id],
                ["gruppo", $request->gruppo],
                ["sezione", $request->old_sezione]
            ])->update(["sezione" => $request->new_sezione]);
            return response(["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function getOrdersListByDate(Request $request)
    {
        $validate = $request->validate([
            "starting_date" => "date_format:Y-m-d",
            "ending_date" => "date_format:Y-m-d"
        ]);
        try {
            $orders = Order_menu::where([
                ["created_at", ">=", $request->starting_date],
                ["created_at", "<", $request->ending_date]
            ])->limit(150)->orderBy("created_at", "desc")->get();
            return response(OrderResource::collection($orders));
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function getOrdersList(Request $request)
    {
        $validate = $request->validate([
            "range" => "sometimes|nullable|integer",
            "start_from" => "sometimes|nullable|integer"
        ]);

        try {
            $range = isset($request->range) ? $request->range : 50;
            $data = [];
            if (!isset($request->start_from)) {
                $data = Order_menu::whereNull("closed_at")->limit($range)->orderBy("created_at", "desc")->get();
            } else {
                $data = Order_menu::whereNull("closed_at")->orderBy("created_at", "desc")->skip($request->start_from)->take($range)->get();
            }

            /*$orders = [];
            foreach ($data as $order) {
                array_push($orders, [
                    "client" => User::find($order["client_id"])->username,
                    "created_at" =>  Carbon::parse($order["created_at"])->format('d/m/Y H:i:s'),
                    "code" => $order["code"],
                    "id" => $order["id"],
                    "menu_id" => $order["menu_id"],
                    "quantity" => $order["quantity"],
                    "richiesta" => $order["richiesta"],
                ]);
            }*/
            return response(OrderResource::collection($data));
            //return response($orders);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function getOrderListCodes(Request $request)
    {
        $validate = $request->validate([
            "start_from" => "sometimes|nullable|integer"
        ]);

        try {
            $orders = [];
            if (isset($request->start_from)) {
                $orders = Order_menu::whereNull("closed_at")
                    ->select("code", "created_at", "client_id")
                    ->orderBy("created_at", "desc")
                    ->groupBy("code","created_at","client_id")
                    ->skip($request->start_from)
                    ->take(50)
                    ->get();
            } else {
                $orders = Order_menu::whereNull("closed_at")
                    ->select("code", "created_at", "client_id")
                    ->orderBy("created_at", "desc")
                    ->groupBy("code","created_at","client_id")
                    ->limit(100)
                    ->get();
            }

            return response(OrderMenuResource::collection($orders));
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova', "exception" =>$exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }



    public function getOpenProductsInstance(Request $request)
    {
        $validate = $request->validate([
            "order" => "required|string",
            "start_from" => "sometimes|nullable|integer",
        ]);

        try {
            $productsInstances = [];
            if (isset($request->start_from)) {
                $productsInstances = ProductInstance::where([
                    ["order", $request->order],
                ])->orderBy("scanned_at", "asc")->skip($request->start_from)->take(100)->get();
            } else {
                $productsInstances = ProductInstance::where([
                    ["order", $request->order],
                ])->orderBy("scanned_at", "asc")->limit(100)->get();
            }
            return response(ProductIntsanceResource::collection($productsInstances));
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }
}
