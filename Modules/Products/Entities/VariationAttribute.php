<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariationAttribute extends Model{
    use SoftDeletes;

    protected $table = 'pm_product_variation_attributes';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];

    public function variation(){
        return $this->belongsTo(\Modules\Products\Entities\ProductVariation::class,'variation_id');
    }
    public function type(){
        return $this->belongsTo(\Modules\Products\Entities\AttributeType::class, 'type_id');
    }
    
}
