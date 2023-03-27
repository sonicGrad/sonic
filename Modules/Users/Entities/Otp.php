<?php

namespace Modules\Users\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Otp extends Model{
    protected $table = 'um_otps';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];

    
}
