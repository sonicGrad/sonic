<?php

namespace Modules\Users\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermissionGroup extends Model{
    protected $table = 'um_permission_groups';

    public $timestamps = false;

    protected $appends = ['name'];
    
    public function permissions(){
        return $this->hasMany(\Spatie\Permission\Models\Permission::class, 'group_id');
    }
    
    public function childrenGroups(){
        return $this->hasMany(\Modules\Users\Entities\PermissionGroup::class, 'parent_id')->orderByOrderNo();
    }
    
    public function allChildrenGroups(){
        return $this->childrenGroups()->with(['allChildrenGroups', 'permissions']);
    }

    public function getNameAttribute(){
        return $this->name_ar;
    }

    public function scopeOrderByOrderNo($query){
        return $query->orderByRaw("ISNULL(order_no) ASC, order_no ASC");
    }
}
