<?php

namespace App\Http\Resources;

use App\Models\Ingredient;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredientQuantity extends JsonResource
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
            "ingredient" => Ingredient::find($this->ingredient_id)->name,
            "quantity" => $this->quantity 
        ];
    }
}
