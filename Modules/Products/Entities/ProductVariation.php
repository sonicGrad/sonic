<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariation extends Model{
    use SoftDeletes;

    protected $table = 'pm_product_variations';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new \App\Scopes\ActiveForVariationScope);
    }
    
    public function product(){
        return $this->belongsTo(\Modules\Products\Entities\Product::class, 'product_id');
    }
    public function attributes(){
        return $this->hasMany(\Modules\Products\Entities\VariationAttribute::class, 'variation_id');
    }
}
