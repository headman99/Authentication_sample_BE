<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menu';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'nome',
        'descrizione',
        'active',
        'created_at',
        'updated_at'
    ];

    public function menu_receips(){
        return $this->hasMany('App\Models\MenuReceips','menu_id','id');

    }
    public function menu_instance(){
        return $this->hasMany('App\Models\MenuInstance','menu_id','id');

    }
}
