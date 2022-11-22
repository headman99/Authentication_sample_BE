<?php

namespace App\Http\Resources;

use App\Models\Menu;
use App\Models\MenuRecipe;
use App\Models\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuPaginateResource extends JsonResource
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
            'menu' => new MenuResource($this->menu),
            //'products'=>$this->products
        ];
    }
}
