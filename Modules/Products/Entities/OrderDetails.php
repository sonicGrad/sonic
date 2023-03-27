<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetails extends Model{
    use SoftDeletes;

    protected $table = 'pm_order_details';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new \App\Scopes\OrderDetailsActiveProductsScope);
    }
    public function product(){
        return $this->belongsTo(\Modules\Products\Entities\Product::class)->withoutGlobalScope('ActiveScope');
    }
    public function variation(){
        return $this->belongsTo(\Modules\Products\Entities\ProductVariation::class, 'variation_id');
    }
}
