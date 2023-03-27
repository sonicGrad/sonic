<?php

namespace Modules\Users\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Favorite extends Model{
    protected $table = 'um_favorites';
    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new \App\Scopes\ActiveStateForFavorite);
    }
    public function user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class);
    }
    public function product(){
        return $this->belongsTo(\Modules\Products\Entities\Product::class);
    }
   
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
}
