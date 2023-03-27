<?php

namespace Modules\CMS\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactUs extends Model{
    use SoftDeletes;

    protected $table = 'cms_contact_us';
    public function created_by_user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class, 'created_by');
    }
   
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];   
}
