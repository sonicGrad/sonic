<?php

namespace Modules\Vendors\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model{
    use SoftDeletes;
    use HasTranslations;

    protected $table = 'vn_coupons';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new \App\Scopes\ActiveStatusNoIdScope);
        static::addGlobalScope(new \App\Scopes\AdminActiveScope);
    }
    public function scopeActive($query){
        return $query->where(function($query){
           $query->where('status', 1);
        });
    }
    public function scopeAdminActive($query){
        return $query->where(function($query){
           $query->where('admin_status', 1);
        });
    }
    public function created_by_user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class, 'created_by');
    }
    public function vendor(){
        return $this->belongsTo(\Modules\Vendors\Entities\Vendors::class,'vendor_id');
    }
    public $translatable = ['description', 'name'];

    public function users(){
        return   $this->belongsToMany(\Modules\Users\Entities\User::class, 'um_user_coupons');
    }
    public function type(){
        return   $this->belongsTo(\Modules\Vendors\Entities\CouponsType::class);
    }
    public function admin_status(){
        return $this->belongsTo(\Modules\Users\Entities\AdminStatusForVendorActivity::class,'admin_status');
    }

}
