<?php

namespace App\Http\Controllers;

use App\Models\MenuRecipe;
use App\Models\Order_menu;
use App\Models\ProductInstance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class CustomerController extends Controller
{
    public function addOrderMenu(Request $request)
    {
        $validate = $request->validate([
            "data" => "required|array|min:1",
            'data.*.quantity' => 'required|integer',
            'data.*.menu_id' => 'required',
            //"data.*.richiesta" => "nullable|sometimes|string|max:250"
        ]);

        try {
            DB::beginTransaction();
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $code = null;

            $randomString = '';
            foreach ($request->data as $obj) {
                do {
                    $code = Str::random(6);
                    #Verifica che non esistono duplicati del codice 
                    $duplicates =  Order_menu::where("code", $code)->first();
                } while (isset($duplicates));

                Order_menu::create([
                    "menu_id" => $obj["menu_id"],
                    "code" => $code,
                    "quantity" => $obj["quantity"],
                    "client_id" => Auth::id(),
                    "richiesta" => isset($obj["richiesta"]) ? $obj["richiesta"] : ''
                ]);
                $products_id = MenuRecipe::where("menu_id", $obj["menu_id"])->select("product_id")->get();
                
                for ($i = 0; $i < $obj["quantity"]; $i++) {
                    $completedMenu = [];
                    foreach ($products_id as $product) {

                        $charactersLength = strlen($characters);
                        $randomString = $code . '_';
                        for ($p = 0; $p < 10; $p++) {
                            $randomString .= $characters[rand(0, $charactersLength - 1)];
                        }
                        array_push($completedMenu,[
                            "product_id" => $product->product_id,
                            "barcode" => $randomString,
                            "order" => $code,
                            "created_at"=>Carbon::now(),
                            "updated_at"=>Carbon::now(),
                        ]);
                    }
                    ProductInstance::insert($completedMenu);
                }
            }
            DB::commit();
            return response(["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage(), "product" => $product], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }
}
