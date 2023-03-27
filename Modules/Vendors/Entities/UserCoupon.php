<?php

namespace Modules\Vendors\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCoupon extends Model{
    use SoftDeletes;

    protected $table = 'um_user_coupons';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];

    public function coupon(){
        return $this->belongsTo(\Modules\Vendors\Entities\Coupon::class);
    }
}
