<?php

namespace App\Http\Resources;

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
            "Prodotto" => Product::find($this->product_id)->nome,
            "Ordine" => $this->order,
            "Creato il" => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
            "barcode" => $this->barcode
        ];
    }
}
