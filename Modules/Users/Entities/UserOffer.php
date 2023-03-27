<?php

namespace Modules\Users\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserOffer extends Model{
    use SoftDeletes;

    protected $table = 'um_user_offers';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    
    public function user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class, 'user_id');
    }
    public function product(){
        return $this->belongsTo(\Modules\Products\Entities\Product::class, 'product_id');
    }
    public function offer(){
        return $this->belongsTo(\Modules\Vendors\Entities\Offer::class, 'offer_id');
    }
}
