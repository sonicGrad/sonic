<?php
  
namespace App\Scopes;
  
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
  
class ActiveForVariationScope implements Scope{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->when((!auth()->user()->hasRole('super_admin') && !auth()->user()->hasRole('vendor')), function($builder) {
            $builder->whereHas('product', function ($query) {
                $query->where('pm_products.status_id', '1' );
            });
            $builder->whereHas('product', function ($query) {
                $query->where('pm_products.admin_status', '1' );
                
            });
            $builder->whereHas('product.vendor', function ($query) {
                $query->where('vn_vendors.status_id', '1' );
    
            })->whereHas('product.category', function ($query) {
                $query->where('pm_categories.status_id', '1' );
    
            });
        });
    }
}
