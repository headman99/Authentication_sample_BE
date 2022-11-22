<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            "id" => $this->menu_id,
            "nome"=> $this->nome,
            "descrizione" => $this->descrizione,
            "active" => $this->active,
            "created_at" => $this->created_at,
            "updated_at"=>$this->updated_at
        ];
    }
}
