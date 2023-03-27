<?php

namespace Modules\Users\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reason extends Model{
    use SoftDeletes;
    protected $table = 'um_reasons';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    public function rateable(){
        return $this->morphTo('reasonable', 'reasonable_type', 'reasonable_id', 'id');
    }
}
