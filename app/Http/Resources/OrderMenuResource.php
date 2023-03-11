<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

    class OrderMenuResource extends JsonResource
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
            "codice" => $this->code,
            "data_evento" => Carbon::parse($this->event_date)->format('d-m-Y'),
            "cliente" => User::find($this->client_id)->username,
            "creato il" => Carbon::parse($this->created_at)->format('d-m-Y H:i:s')
        ];
    }
}
