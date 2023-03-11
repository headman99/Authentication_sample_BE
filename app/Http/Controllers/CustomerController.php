<?php

namespace App\Http\Controllers;

use App\Models\MenuRecipe;
use App\Models\Order_menu;
use App\Models\Product;
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
            "data.*.date" => "required",
            "data.*.richiesta" => "nullable|sometimes|string|max:250",
            "data.*.alternative" => "sometimes|nullable|array"
        ]);

        try {
            DB::beginTransaction();
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $code = null;

            foreach ($request->data as $obj) {
                if ($obj["date"] <= Carbon::now())
                    return response(["message" => "la data inserita deve essere postuma a quella odierna",], \Illuminate\Http\Response::HTTP_BAD_REQUEST);

                $code = Str::random(6);
                $order = Order_menu::create([
                    "menu_id" => $obj["menu_id"],
                    "code" => $code,
                    "event_date" => Carbon::parse($obj["date"])->format("Y-m-d"),
                    "quantity" => $obj["quantity"],
                    "client_id" => Auth::id(),
                    "richiesta" => isset($obj["richiesta"]) ? $obj["richiesta"] : ''
                ]);

                $products = MenuRecipe::where([
                    ["menu_id", $obj["menu_id"]],
                    ["alternative", NULL],
                ])->select("product_id", "ratio")
                    ->groupby("product_id", "ratio")
                    ->get();



                foreach ($products as $key => $product) {
                    $toInsert = [];
                    for ($i = 0; $i < intval($obj["quantity"] * $product->ratio); $i++) {
                        $charactersLength = strlen($characters);
                        $randomString = $code . '_';
                        for ($p = 0; $p < 10; $p++) {
                            $randomString .= $characters[rand(0, $charactersLength - 1)];
                        }
                        array_push($toInsert, [
                            "product_id" => $product->product_id,
                            "barcode" => $randomString,
                            "order" => $order->id,
                            "created_at" => Carbon::now(),
                            "updated_at" => Carbon::now(),
                            "page" => $key+1
                        ]);
                    }
                    ProductInstance::insert($toInsert);
                }


                //Get all alternatives if any
                if ($obj['alternative'] && count($obj['alternative']) > 0) {
                    //$product_ids = $products_alternatives->pluck('product_id');
                    foreach ($obj["alternative"]  as $alt) {
                        $product = Product::where('nome', $alt['alternative'])->first()->id;
                        $alternative = MenuRecipe::where([
                            ["menu_id", $obj["menu_id"]],
                            ["product_id", $product],
                            ["gruppo", $alt['gruppo']],
                            ["sezione", $alt["sezione"] ? $alt['sezione'] : '']
                        ])->whereNotNull("alternative")->first();

                        ProductInstance::where([
                            ["product_id", $alternative->alternative],
                            ["order", $order->id]
                        ])->update(["product_id" =>  $alternative->product_id]);
                    }
                }
            }
            DB::commit();
            return response(["state" => 1]);
        } catch (\Exception $exc) {
            Log::error($exc->getMessage());
            return response(['message' => "Qualcosa è andato storto, riprova", "exception" => $exc->getMessage()], \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
    }
}
