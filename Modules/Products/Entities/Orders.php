<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orders extends Model{
    use SoftDeletes;

    protected $table = 'pm_orders';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    protected static function boot(){
        parent::boot();
        // static::addGlobalScope(new \App\Scopes\ActiveVendorScope);
    }
    public function order_details(){
       return $this->hasMany(\Modules\Products\Entities\OrderDetails::class,'order_id');
    }
    public function coupon_for_user(){
       return $this->hasOne(\Modules\Vendors\Entities\UserCoupon::class,'order_id');
    }

    public function user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class, 'buyer_id');
    }
    public function vendor(){
        return $this->belongsTo(\Modules\Vendors\Entities\Vendors::class, 'seller_id');
    }
    public function driver(){
        return $this->belongsTo(\Modules\Drivers\Entities\Driver::class, 'driver_id');
    }
    public function last_status(){
        return $this->belongsTo(\Modules\Products\Entities\OrderState::class, 'last_status');
    }
    public function offer(){
        return $this->hasOne(\Modules\Users\Entities\UserOffer::class, 'order_id');
    }
    public function coupon(){
        return $this->hasOne(\Modules\Users\Entities\UserCoupon::class, 'order_id');
    }
    public function add_status(){
        return $this->hasMany(\Modules\Products\Entities\OrderState::class, 'order_id');
    }
   
   public function homePageDriver(){
    # code...
   }
   
}
