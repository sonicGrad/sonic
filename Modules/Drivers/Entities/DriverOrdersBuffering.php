<?php

namespace Modules\Drivers\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverOrdersBuffering extends Model{
    use SoftDeletes;
    protected $table = 'dr_driver_orders_buffering';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    
    public function order(){
        return $this->belongsTo(\Modules\Products\Entities\Orders::class, 'order_id');
    }
    public function driver(){
        return $this->belongsTo(\Modules\Drivers\Entities\Driver::class, 'driver_id');
    }
}
