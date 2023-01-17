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

class CustomerController extends Controller
{
    public function addOrderMenu(Request $request)
    {
        $validate = $request->validate([
            "data" => "required|array|min:1",
            'data.*.quantity' => 'required|integer',
            'data.*.menu_id' => 'required',
            "data.*.date"=> "required",
            "data.*.richiesta" => "nullable|sometimes|string|max:250"
        ]);

        try {
            DB::beginTransaction();
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $code = null;

            foreach ($request->data as $obj) {
                if($obj["date"] <= Carbon::now())
                    return response (["message"=>"la data inserita deve essere postuma a quella odierna", ],\Illuminate\Http\Response::HTTP_BAD_REQUEST);

                $code = Str::random(6);
                $order = Order_menu::create([
                    "menu_id" => $obj["menu_id"],
                    "code" => $code,
                    "event_date" => Carbon::parse($obj["date"])->format("d/m/Y"),
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
                            "order" => $order->id,
                            "created_at"=>Carbon::now(),
                            "updated_at"=>Carbon::now(),
                            "page"=>$i+1
                        ]);
                    }
                    ProductInstance::insert($completedMenu);
                }
            }
            DB::commit();
            return response(["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage(), ], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }
}
