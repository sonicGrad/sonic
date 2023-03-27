<?php

namespace Modules\Users\Entities;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model{
    protected $table = 'um_ratings';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    public function rateable(){
        return $this->morphTo('rateable', 'rateable_type', 'rateable_id', 'id');
    }

    public function product(){
        return $this->belongsTo(\Modules\Products\Entities\Product::class);
    }
    public function user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class);
    }
}
