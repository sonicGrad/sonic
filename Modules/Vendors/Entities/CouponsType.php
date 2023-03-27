<?php

namespace Modules\Vendors\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class CouponsType extends Model{
    use HasTranslations;
    use SoftDeletes;

    protected $table = 'vn_coupons_types';
   
    public $translatable = ['name'];
    
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
}
