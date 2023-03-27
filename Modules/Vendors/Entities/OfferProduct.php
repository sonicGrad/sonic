<?php

namespace Modules\Vendors\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferProduct extends Model{ 
    use SoftDeletes;

    protected $table = 'vn_offers_products';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new \App\Scopes\OrderDetailsActiveProductsScope);
    }
    
    public function product(){
        return $this->belongsTo(\Modules\Products\Entities\Product::class);
    }
    public function offer(){
        return $this->belongsTo(\Modules\Vendors\Entities\Offer::class);
    }
}