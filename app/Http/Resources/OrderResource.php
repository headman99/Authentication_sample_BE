<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            "id" =>$this->id,
            "menu_id" => $this->menu_id,
            "code" => $this->code,
            "client" => User::find($this->client_id)->username,
            "quantity" =>$this->quantity,
            "richiesta" => $this->richiesta,
            "created_at" => Carbon::parse($this->created_at)->format('d/m/Y H:i:s')
        ];
    }
}
