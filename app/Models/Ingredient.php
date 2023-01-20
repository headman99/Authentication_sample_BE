<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ingredients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'description',
        'category',
        'provider',
        'created_at',
        'updated_at',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function stoks()
    {
        return $this->hasMany('App\Models\Stoks', 'ingredient_id', 'id');
    }

    public function productreceips()
    {
        return $this->hasMany('App\Models\ProductsReceips', 'ingredient_id', 'id');
    }
}
