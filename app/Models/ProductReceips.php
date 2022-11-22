<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReceips extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_receips';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'ingredient_id',
        'quantity',
        'created_at',
        'updated_at'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function productid()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function ingredientid()
    {
        return $this->belongsTo('App\Models\Ingredient', 'ingredient_id', 'id');
    }

}
