<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_menu extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'orders_menu';

    protected $fillable = [
        'id',
        "code",
        "menu_id",
        "client_id",
        "quantity",
        "richiesta",
        "created_at",
        "updated_at",
        "closed_at"
    ];

    public function menu(){
        return $this->belongsTo('App\Models\Menu','menu_id','id');
    }

    public function user(){
        return $this->belongsTo('App\Models\User','client_id','id');
    }

}
