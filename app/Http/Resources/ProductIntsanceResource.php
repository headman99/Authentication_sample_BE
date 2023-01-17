<?php

namespace App\Http\Resources;

use App\Models\Order_menu;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductIntsanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "prodotto" => Product::find($this->product_id)->nome,
            "ordine" => Order_menu::find($this->order)->code,
            "creato_il" => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
            "operatore" => $this->operator,
            "barcode" => $this->barcode,
            "scanned_at" => $this->scanned_at,
            "page" =>$this->page,
        ];
    }
}
