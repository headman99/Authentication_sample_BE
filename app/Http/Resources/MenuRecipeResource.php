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
            "id" => $this->id,
            "nome" => $this->nome,
            "gruppo" => $this->gruppo,
            "sezione" => $this->sezione,
            "alternative"=>$this->alternative?Product::find($this->alternative)->nome:NULL,
            "ratio" => $this->ratio,
            "groupPosition" => $this->groupPosition
        ];
    }
}
