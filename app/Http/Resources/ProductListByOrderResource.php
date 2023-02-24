<?php

namespace App\Http\Resources;

use App\Models\Order_menu;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListByOrderResource extends JsonResource
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
            'order' =>$this->order,
            "id" => $this->product_id,
            "product" => Product::find($this->product_id)->nome,
            "quantity" => $this->quantity,
            "checked" => $this->final_check==1?true:false
        ];
    }
}
