<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feature extends Model{
    use SoftDeletes;
    protected $table = 'core_features';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    public function typeable(){
        return $this->morphTo();
    }
}
