<?php

namespace App\Http\Resources;

use App\Models\Stoks;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamIngredientsByProductRecipe extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $pz = Stoks::where("ingredient_id", $this->id)->first()->pz;
        return [
            "id"=>$this->id,
            "name" => $this->name,
            "quantity" => $this->quantity,
            "pz" => $pz===0?false:true
        ];
    }
}
