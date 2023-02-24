<?php

namespace App\Http\Controllers;

use App\Http\Resources\IngredientResource;
use App\Http\Resources\MenuPaginateResource;
use App\Http\Resources\MenuRecipeResource;
use App\Http\Resources\OrderMenuResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\StockResource;
use App\Http\Resources\ProductIntsanceResource;
use App\Http\Resources\ProductListByOrderResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\TeamIngredientResource;
use App\Http\Resources\TeamIngredientsByProductRecipe;
use App\Http\Resources\TeamResource;
use App\Models\Ingredient;
use App\Models\Menu;
use App\Models\MenuRecipe;
use App\Models\Order_menu;
use App\Models\Product;
use App\Models\ProductInstance;
use App\Models\ProductReceips;
use App\Models\Stoks;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Hamcrest\Arrays\IsArray;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class AdminController extends Controller
{
    public function registerIngredient(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|max:50|string',
            'description' => 'sometimes|nullable|string|max:250',
            'quantity' => 'required|integer|min:0',
            'category' => 'sometimes|nullable|string|max:20',
            'provider' => 'sometimes|nullable|string|max:50',
            "team" => "sometimes|nullable|integer"
        ]);

        try {

            $checkValidity = Ingredient::where('name', $request->name)->first();
            if ($checkValidity)
                return response(['message' => 'Inserisci un nome diverso da quelli già presenti'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
            $ingredient = Ingredient::create($validate);
            Stoks::create([
                'ingredient_id' => $ingredient->id,
                'quantity' => $request->quantity ? $request->quantity : 0,
            ]);

            return response(new IngredientResource($ingredient));
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova', "error" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
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
                Stoks::where('ingredient_id', $obj['ingredient'])->update(['quantity' => $obj['quantity'] ? $obj['quantity'] : 0]);
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
        try {
            $catalog = Product::orderBy("nome")->get();
            return (ProductResource::collection($catalog));
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova'], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function getStock()
    {
        $stoks = DB::table('stoks')
            ->join('ingredients', 'ingredient_id', '=', 'ingredients.id')
            ->select('ingredient_id as id', 'ingredients.name', 'stoks.quantity', 'ingredients.category', "ingredients.provider", "ingredients.team")
            ->orderBy('ingredients.name')
            ->get();
        return (StockResource::collection($stoks));
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
            'categoria' => 'nullable|sometimes|string|max:10',
            'gruppo' => "nullable|string|sometimes",
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
            "start_from" => "sometimes|nullable|integer",
            "start_date" => "sometimes|nullable",
            "end_date" => "sometimes|nullable",
            "closed" => "sometimes|nullable"
        ]);

        try {
            $range = isset($request->range) ? $request->range : 50;
            $skip = isset($request->start_from) ? $request->start_from : 0;
            $data = [];
            $filter_date_condition = [];



            if (isset($request->start_date)) {
                array_push(
                    $filter_date_condition,
                    ["created_at", ">=", $request->start_date]
                );
            }


            if (isset($request->end_date)) {
                array_push(
                    $filter_date_condition,
                    ["created_at", "<=", Carbon::parse($request->end_date)->addDay()]
                );
            }

            if (!$request->closed) {
                if (sizeof($filter_date_condition) > 0) {
                    $data = Order_menu::whereNull("closed_at")
                        ->where($filter_date_condition)
                        ->orderBy("created_at", "desc")
                        ->skip($skip)
                        ->take($range)
                        ->get();
                } else {
                    $data = Order_menu::whereNull("closed_at")
                        ->orderBy("created_at", "desc")
                        ->skip($skip)
                        ->take($range)
                        ->get();
                }
            } else {
                if (sizeof($filter_date_condition) > 0) {
                    $data = Order_menu::whereNotNull("closed_at")
                        ->where($filter_date_condition)
                        ->orderBy("created_at", "desc")
                        ->skip($skip)
                        ->take($range)
                        ->get();
                } else {
                    $data = Order_menu::whereNotNull("closed_at")
                        ->orderBy("created_at", "desc")
                        ->skip($skip)
                        ->take($range)
                        ->get();
                }
            }

            return response(OrderResource::collection($data));
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
            if (!empty($request->start_from)) {
                $orders = Order_menu::whereNull("closed_at")
                    ->select("id", "code", "created_at", "client_id")
                    ->orderBy("created_at", "desc")
                    ->skip($request->start_from)
                    ->take(50)
                    ->get();
            } else {
                $orders = Order_menu::whereNull("closed_at")
                    ->select("id", "code", "created_at", "client_id")
                    ->orderBy("created_at", "desc")
                    ->limit(100)
                    ->get();
            }

            return response(OrderMenuResource::collection($orders));
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => 'Qualcosa è andato storto, riprova', "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function scanProduct(Request $request)
    {
        $validate = $request->validate([
            "barcode" => "required|string|max:30",
        ]);

        try {
            DB::beginTransaction();
            $product = ProductInstance::where("barcode", $request->barcode)->first();
            if (!isset($product))
                return response(["message" => "codice non esistente"], \Illuminate\Http\Response::HTTP_BAD_REQUEST);

            if (isset($product->scanned_at))
                return response(["message" => "Codice già scansionato"], \Illuminate\Http\Response::HTTP_BAD_REQUEST);

            $product->update(
                [
                    "scanned_at" => Carbon::now()->format("Y-m-d"),
                    "operator" => Auth::user()->badge
                ],
            );
            $otherProducts = ProductInstance::where("order", $product->order)->whereNull("scanned_at")->first();
            if (!isset($otherProducts)) {
                Order_menu::find($product->order)->update(["closed_at" => Carbon::now()->format("Y-m-d")]);
            }
            DB::commit();
            return response(["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }


    public function getOpenProductsInstance(Request $request)
    {
        $validate = $request->validate([
            "order" => "required|string|max:6",
            "start_from" => "sometimes|nullable|integer",
            "page" => "sometimes|nullable|integer|min:1"
        ]);

        try {
            $productsInstances = [];
            $order = Order_menu::where("code", $request->order)->first();
            if (isset($request->start_from)) {
                $productsInstances = ProductInstance::where([
                    ["order", $order->id],
                    ["page", $request->page ? $request->page : 1]
                ])->orderBy("scanned_at", "asc")->skip($request->start_from)->take(150)->get();
            } else {
                $productsInstances = ProductInstance::where([
                    ["order", $order->id],
                    ["page", $request->page ? $request->page : 1]
                ])->orderBy("scanned_at", "asc")->limit(150)->get();
            }
            if (isset($request->page))
                return response(ProductIntsanceResource::collection($productsInstances));
            else
                return response(["products" => ProductIntsanceResource::collection($productsInstances), "limitPage" => ProductInstance::where("order", $order->id)->max("page")]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function getProductsInstanceByFilter(Request $request)
    {
        $validate = $request->validate([
            "order" => "required|string|max:6",
            "filter" => "required|string"
        ]);

        try {
            $order = Order_menu::where("code", $request->order)->first();
            $productsInstances = DB::table("product_instance")
                ->where("order", $order->id)
                ->join("products", "product_instance.product_id", '=', "products.id")
                ->join("orders_menu", "product_instance.order", '=', 'orders_menu.id')
                ->select("product_instance.id", "products.nome as prodotto", "product_instance.barcode", "product_instance.created_at as creato_il", "product_instance.scanned_at as scanned_at")
                ->where("products.nome", 'like', $request->filter . '%')
                ->orderBy("products.nome")
                ->get();

            return response($productsInstances);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function getTeams(Request $request)
    {
        $teams = Team::orderby('name')->get();
        return response(TeamResource::collection($teams));
    }

    //This takes the productsInstances
    public function getTeamProductListByOrder(Request $request)
    {
        $validate = $request->validate([
            "order" => "required|string",
            "team_id" => "sometimes|nullable"
        ]);
        try {
            
            $order = Order_menu::where("code", $request->order)->first();
            
           /*$products = DB::table("ingredients")
                ->where("ingredients.team",$request->team_id)
                ->join("products_recipes", "ingredients.id",'=','products_recipes.ingredient_id')
                ->select("products_recipes.product_id as product_id")
                ->groupBy("products_recipes.product_id")
                ->get();*/
            $ingredients = Ingredient::where("team",$request->team_id)->get()->pluck('id');
            $prods = ProductReceips::whereIn("ingredient_id",$ingredients)->select("product_id")->groupby("product_id")->get();

            $productsInstances = ProductInstance::where("order", $order->id)
                ->whereIn("product_id",$prods)
                ->select("product_id", "order", DB::raw('count(*) as quantity'), DB::raw('CASE WHEN SUM(CASE WHEN checked = true THEN 1 ELSE 0 END) = COUNT(*) THEN true ELSE false END as final_check'))
                ->groupBy("product_id", "order")
                ->get();

           
            //return response(["data" => $products], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
            return (ProductListByOrderResource::collection($productsInstances));
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    /*public function getProductstByTeam(Request $request)
    {
        $validate = $request->validate([
            "team_id" => "required|integer",
        ]);
        try {
            $products = Product::where("team",$request->team_id)->get();
            return response(ProductResource::collection($products));
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }*/

    public function getIngredientsTeam(Request $request)
    {
        $validate = $request->validate([
            "team_id" => "required|integer",
        ]);
        try {
            $products = Ingredient::where("team", $request->team_id)->get();
            return response(IngredientResource::collection($products));
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function checkProductList(Request $request)
    {
        $validate = $request->validate([
            "order" => "required|integer",
            "product_id" => "required|integer",
            "value" => "required|boolean"
        ]);

        try {
            DB::beginTransaction();
            ProductInstance::where([
                ["product_id", $request->product_id],
                ["order", $request->order],
            ])->update(['checked' => $request->value]);
            DB::commit();
            return (["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }


    public function updateIngredient(Request $request)
    {
        $validate = $request->validate([
            "id" => "required|integer",
            "name" => "sometimes|string",
            "category" => "sometimes|nullable|string",
            "provider" => "sometimes|nullable|string",
            "quantity" => "sometimes|nullable|integer",
            "team" => "sometimes|nullable|string"
        ]);

        try {
            DB::beginTransaction();
            $name = Ingredient::where([
                ["name", $request->name],
                ["id", '<>', $request->id]
            ])->first();

            if ($name)
                return response(['message' => "Nome Ingrediente già presente, sceglierne un altro"], \Illuminate\Http\Response::HTTP_BAD_REQUEST);

            Ingredient::find($request->id)->update([
                "name" => $request->name ? $request->name : $name->name,
                "category" => $request->category,
                "provider" => $request->provider,
                "team" => $request->team ? Team::where("name", $request->team)->first()->id : NULL
            ]);

            if (isset($request->quantity))
                Stoks::where("ingredient_id", $request->id)->update(["quantity" => $request->quantity ? $request->quantity : 0]);
            DB::commit();
            return (["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateProduct(Request $request)
    {
        $validate = $request->validate([
            "id" => "required|integer",
            "nome" => "sometimes|string",
            "categoria" => "sometimes|nullable|string",
            "gruppo" => "sometimes|nullable|string",
        ]);

        try {
            DB::beginTransaction();
            $name = Product::where([
                ["nome", $request->nome],
                ["id", '<>', $request->id]
            ])->first();

            if ($name)
                return response(['message' => "Nome Prodotto già presente, sceglierne un altro"], \Illuminate\Http\Response::HTTP_BAD_REQUEST);

            Product::find($request->id)->update([
                "nome" => $request->nome,
                "categoria" => $request->categoria,
                "gruppo" => $request->gruppo,
            ]);

            DB::commit();
            return (["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function removeTeam(Request $request)
    {
        $validate = $request->validate([
            "id" => "required|integer",
        ]);

        try {
            Team::find($request->id)->delete();
            return (["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateTeam(Request $request)
    {
        $validate = $request->validate([
            "id" => "required|integer",
            "name" => "nullable|sometimes|string",
        ]);

        try {

            $team = Team::where("id", $request->id)->update(["name" => $request->name]);
            return (["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function addTeam(Request $request)
    {
        $validate = $request->validate([
            "name" => "required|string",
        ]);

        try {
            Team::create($validate);
            return (["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    /*public function updateProductsTeam(Request $request){
        Validator::make($request->all(),[
            "team_id" => "required|integer",
            "add_products_id" => "array",
            "remove_products_id" => "array"
        ]);

        try {
            DB::beginTransaction();
            if(!empty($request->add_products_id))
                Product::whereIn("id",$request->add_products_id)->update(["team" => $request->team_id]);
            if(!empty($request->remove_products_id))
                Product::whereIn("id",$request->remove_products_id)->update(["team" => NULL]);
            DB::commit();
            return (["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }*/
    public function updateIngredientsTeam(Request $request)
    {
        Validator::make($request->all(), [
            "team_id" => "required|integer",
            "add_ingredients_id" => "array",
            "remove_ingredients_id" => "array"
        ]);

        try {
            DB::beginTransaction();
            if (!empty($request->add_ingredients_id))
                Ingredient::whereIn("id", $request->add_ingredients_id)->update(["team" => $request->team_id]);
            if (!empty($request->remove_ingredients_id))
                Ingredient::whereIn("id", $request->remove_ingredients_id)->update(["team" => NULL]);
            DB::commit();
            return (["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }

    public function getTeamIngredientsByProductRecipe(Request $request)
    {
        $validate = $request->validate([
            "product_id" => "required|integer",
            "team_id" => "sometimes|nullable|integer"
        ]);

        try {
            /*$recipes = ProductReceips::where("product_id",$request->product_id)->get()->pluck("product_id");
            $ingredients = Ingredient::where("team",$request->team_id)
                ->whereIn("id",$recipes)
                ->get();*/
            $ingredients = [];
            if ($request->team_id)
                $ingredients = DB::table("products_recipes")
                    ->where("product_id", $request->product_id)
                    ->join("ingredients", "products_recipes.ingredient_id", '=', 'ingredients.id')
                    ->where("ingredients.team", $request->team_id)
                    ->select("ingredients.id as id", "ingredients.name as name", "products_recipes.quantity as quantity")
                    ->orderBy("ingredients.name")
                    ->get();
            else
                $ingredients = DB::table("products_recipes")
                    ->where("product_id", $request->product_id)
                    ->join("ingredients", "products_recipes.ingredient_id", '=', 'ingredients.id')
                    ->select("ingredients.id as id", "ingredients.name as name", "products_recipes.quantity as quantity")
                    ->orderBy("ingredients.name")
                    ->get();

            return (TeamIngredientsByProductRecipe::collection($ingredients));
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }
}
