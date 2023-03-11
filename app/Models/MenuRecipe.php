<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuRecipe extends Model
{
    use HasFactory;

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menu_recipes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'menu_id',
        'product_id',
        'gruppo',
        'sezione',
        "alternative",
        "ratio",
        "groupPosition",
        'created_at',
        'updated_at'
    ];

    public function menu(){
        return $this->belongsTo('App\Models\Menu','menu_id','id');

    }
    public function product(){
        return $this->belongsTo('App\Models\MenuInstance','product','id');

    }
}
