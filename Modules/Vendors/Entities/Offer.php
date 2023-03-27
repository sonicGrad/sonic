<?php

namespace Modules\Vendors\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpParser\Node\Stmt\Foreach_;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Offer extends Model implements HasMedia{
    use HasTranslations;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $table = 'vn_offers';
    protected $appends = ['image_url'];
    protected static function boot(){
        parent::boot();
        
        static::addGlobalScope(new \App\Scopes\ActiveStatusNoIdScope);
        static::addGlobalScope(new \App\Scopes\AdminActiveScope);

    }
    public function scopeActive($query){
        return $query->where(function($query){
           $query->where('status_id', 1);
        });
    }
    public function scopeAdminActive($query){
        return $query->where(function($query){
           $query->where('admin_status', 1);
        });
    }
    public function admin_status(){
        return $this->belongsTo(\Modules\Users\Entities\AdminStatusForVendorActivity::class,'admin_status');
    }
    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media  $media = null): void{
        $this->addMediaConversion('thumb')
              ->width(400)
              ->height(400);
    }
    public function getImageUrlAttribute(){
        $image = $this->getMedia('offer-image')->first();

        if($image){
            return url('/') . '/storage/app/public/' . $image->id . '/' . $image->file_name;
        }

        return asset('/public/assets/images/avatars/avatar6.png');

    }
    public function vendor(){
        return $this->belongsTo(\Modules\Vendors\Entities\Vendors::class,'vendor_id');
    }
    public static function getMedias(){
        $offers = \Modules\Vendors\Entities\Offer::get();
        $images_new  = collect([]);
        foreach ($offers as $offer) {
            $images = $offer->getMedia('offer-image');
            foreach($images as $image){
                $new['offer_id'] = $offer->id;
                $new['name'] = $image->file_name;
                $new['url'] =  url('/') . '/storage/app/public/' . $image->id . '/' . $image->file_name;
                $images_new->push($new);
            }
        }
        return response()->json($images_new,200);
    }
    public function created_by_user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class, 'created_by');
    }
    public function offer_product(){
        return $this->hasMany(\Modules\Vendors\Entities\OfferProduct::class);
    }
    public function type(){
        return   $this->belongsTo(\Modules\Vendors\Entities\CouponsType::class);
    }
    public $translatable = ['name','description'];
    
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    
  
}
