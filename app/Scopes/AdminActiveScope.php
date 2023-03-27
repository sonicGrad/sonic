<?php
  
namespace App\Scopes;
  
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
  
class AdminActiveScope implements Scope{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model){
        if(auth()->guard('api')->user()){
            $builder->when((!auth()->guard('api')->user()->hasRole('super_admin') && !auth()->guard('api')->user()->hasRole('vendor')), function($builder) {
                
                $builder->where('admin_status', '=', 1);
            });
        }else if(auth()->user()){
            $builder->when((!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('vendor')), function($builder) {
                $builder->where('admin_status', '=', 1);
            });
        } 
        else{
            $builder->where('admin_status', '=', 1);
        }
    }
}