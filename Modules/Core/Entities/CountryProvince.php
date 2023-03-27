<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class CountryProvince extends Model{
    use SoftDeletes;
    use HasTranslations;

    protected $table = 'core_country_provinces';
    public $translatable = ['name'];
    
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    
    protected $appends = [];

    public static function boot(){
        parent::boot();

        self::creating(function($model){
            $model->full_name = ($model->country && $model->country->name && trim($model->country->name) !== '' ? $model->country->name . " - " : "") . $model->name;
        });

        self::updating(function($model){
            $model->full_name = ($model->country && $model->country->name && trim($model->country->name) !== '' ? $model->country->name . " - " : "") . $model->name;
        });
    }
    
    public function created_by_user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class, 'created_by');
    }
    
    public function country(){
        return $this->belongsTo(\Modules\Core\Entities\Country::class, 'country_id');
    }

}
