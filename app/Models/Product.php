<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'nome',
        'peso',
        'descrizione',
        'categoria',
        'gruppo',
        'created_at',
        'updated_at'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product_instance()
    {
        return $this->hasMany('App\Models\ProductInstance', 'id', 'id');
    }

    public function product_receips()
    {
        return $this->hasMany('App\Models\ProductsReceips', 'product_id', 'id');
    }
}
