<?php

namespace Modules\Drivers\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverType extends Model{
    use HasTranslations;
    use SoftDeletes;

    protected $table = 'dr_drivers_types';
    public function created_by_user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class, 'created_by');
    }
   
    public $translatable = ['name'];
    
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
}
