<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryAttributeType extends Model{
    use SoftDeletes;

    protected $table = 'pm_product_category_attribute_types';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    
    public function category(){
        return $this->belongsTo(\Modules\Vendors\Entities\TypeOFVendor::class,'category_id');
    }
    public function attribute(){
        return $this->belongsTo(\Modules\Products\Entities\AttributeType::class,'attribute_type_id');
    }
}
