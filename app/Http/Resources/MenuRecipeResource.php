<?php

namespace App\Http\Resources;

use App\Models\Menu;
use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuRecipeResource extends JsonResource
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
            "product" => new ProductResource([$this->product_id]),
            "gruppo" => $this->gruppo,
            "sezione" => $this->sezione,
            "extra"=>$this->extra
        ];
    }
}
