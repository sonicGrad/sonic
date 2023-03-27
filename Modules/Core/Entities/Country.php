<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Country extends Model{
    use HasTranslations;

    protected $table = 'core_countries';
    public $translatable = ['name'];
    
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    
    protected $appends = [];

    public function created_by_user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class, 'created_by');
    }
    
    public function provinces(){
        return $this->hasMany(\Modules\Core\Entities\CountryProvince::class, 'country_id');
    }

}
