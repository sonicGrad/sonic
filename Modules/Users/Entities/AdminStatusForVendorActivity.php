<?php

namespace Modules\Users\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class AdminStatusForVendorActivity extends Model{
    use SoftDeletes;
    use HasTranslations;

    protected $table = 'um_admin_status_for_vendor_activities';
    public function created_by_user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class, 'created_by');
    }
   
    public $translatable = ['name'];
    
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
}
