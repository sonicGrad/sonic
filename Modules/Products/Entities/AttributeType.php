<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class AttributeType extends Model{
    use SoftDeletes;
    use HasTranslations;

    public $translatable = ['name'];
    protected $table = 'pm_product_attribute_types';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
}
