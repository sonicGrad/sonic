<?php

namespace Modules\Products\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia{
    use SoftDeletes;
    use HasTranslations;
    use InteractsWithMedia;
    
    protected $fillable = [
        'product_code','name', 'description','category_id', 'vendor_id', 'currency_id',
        'price','quantity', 'status_id', 'created_by', 'product_code'
    ];
    protected $table = 'pm_products';
    public $translatable = ['name', 'description'];
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    protected $appends = ['image_url', 'price', 'quantity','is_favorite'];
    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new \App\Scopes\ActiveScope);
        static::addGlobalScope(new \App\Scopes\ActiveStateForProducts);
        static::addGlobalScope(new \App\Scopes\AdminActiveScope);
    }
    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media  $media = null): void{
        $this->addMediaConversion('thumb')
              ->width(400)
              ->height(400);
    }
    public function getImageUrlAttribute(){
        $image = $this->getMedia('product-image')->first();

        if($image){
            return url('/') . '/storage/app/public/' . $image->id . '/' . $image->file_name;
        }

        return asset('/public/assets/images/avatars/avatar6.png');

    }
    public function getImagesAttribute(){
        return  $this->getMedia('product-image');
    }
    public function category(){
        return $this->belongsTo(\Modules\Products\Entities\Category::class,'category_id');
    }
    public function vendor(){
        return $this->belongsTo(\Modules\Vendors\Entities\Vendors::class,'vendor_id');
    }
    
    public function created_by_user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class, 'created_by');
    }
    public function status(){
        return $this->belongsTo(\Modules\Products\Entities\ProductStatus::class,'status_id');
    }
    public function admin_status(){
        return $this->belongsTo(\Modules\Users\Entities\AdminStatusForVendorActivity::class,'admin_status');
    }
    public function ratings(){
        return $this->morphMany(\Modules\Users\Entities\Rate::class, 'rateable', 'rateable_type', 'rateable_id', 'id');
    }
    public function type(){
        return $this->morphOne(\Modules\Core\Entities\Feature::class, 'typeable');
    }
    public function favorite(){
        return $this->hasMany(\Modules\Users\Entities\Favorite::class);
    }
    public function offer(){
        return $this->belongsToMany(\Modules\Vendors\Entities\Offer::class, 'vn_offers_products');
    }
    public function variations(){
        return $this->hasMany(\Modules\Products\Entities\ProductVariation::class);
    }
    public function getQuantityAttribute(){
        $variationAttribute =  \Modules\Products\Entities\VariationAttribute::with('variation')->where('type_id', '1')
        ->whereHas('variation.product', function($q){
            $q->where('id', $this->id);
        })->first();
        return $variationAttribute->variation->quantity ?? null;
    }
    public function getPriceAttribute(){
        $variationAttribute =  \Modules\Products\Entities\VariationAttribute::with('variation')->where('type_id', '1')
        ->whereHas('variation.product', function($q){
            $q->where('id', $this->id);
        })->first();
        return $variationAttribute->variation->price ?? null;
    }

    public function getIsFavoriteAttribute(){
        $user = auth()->guard('api')->user();
        if(!$user){
            $user = auth()->guard('web')->user();
        }
        $isFavorite = \Modules\Users\Entities\Favorite::where('user_id', $user->id)
        ->where('product_id', $this->id)
        ->count();
        return $isFavorite;

    }
}
