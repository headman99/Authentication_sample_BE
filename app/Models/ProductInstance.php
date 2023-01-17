<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInstance extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_instance';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'product_id',
        'barcode',
        'order',
        'created_at',
        'updated_at',
        'scanned_at',
        'page',
        "operator"
    ];

    public function product(){
        return $this->belongsTo('App\Models\Product','id','id');
    }

    public function order(){
        return $this->belongsTo("App\Models\Order_menu","code","order");
    }
}
