<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeOfFeature extends Model{
    use SoftDeletes;
    protected $table = 'core_types_of_features';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
}
