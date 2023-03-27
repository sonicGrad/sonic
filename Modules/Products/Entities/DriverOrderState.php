<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverOrderState extends Model{
    use SoftDeletes;

    protected $table = 'pm_driver_order_state';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a','update_at' => 'datetime:Y-m-d H:i:s a'];

    public function order_status(){
        return $this->belongsTo(\Modules\Products\Entities\OrderStatus::class,'status_id');
    }

    public function driver(){
       return $this->belongsTo(\Modules\Drivers\Entities\Driver::class)->withoutGlobalScope('App\Scopes\ActiveScope');
    }

    public function order(){
        return $this->belongsTo(\Modules\Products\Entities\Orders::class,'order_id');
        
    }
}
