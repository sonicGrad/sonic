<?php

namespace Modules\Users\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class AddressBook extends Model{
    use SoftDeletes;
    
    protected $table = 'um_address_books';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];

    public function user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class);
    }
   
}
