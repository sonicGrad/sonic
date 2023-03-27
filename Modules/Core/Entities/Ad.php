<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Ad extends Model implements HasMedia{
    use SoftDeletes;
    use InteractsWithMedia;

    protected $table = 'core_ads';
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];  
    protected $appends = ['image_url'];
    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new \App\Scopes\ActiveScope);
        static::addGlobalScope(new \App\Scopes\AdminActiveScope);
    }
    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media  $media = null): void{
        $this->addMediaConversion('thumb')
              ->width(400)
              ->height(400);
    }
    public function admin_status(){
        return $this->belongsTo(\Modules\Users\Entities\AdminStatusForVendorActivity::class,'admin_status');
    }
    public function getImageUrlAttribute(){
        $image = $this->getMedia('ad-image')->first();

        if($image){
            return url('/') . '/storage/app/public/' . $image->id . '/' . $image->file_name;
        }

        return asset('/public/assets/images/avatars/avatar6.png');

    }
    public function created_by_user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class, 'created_by');
    }
    public function vendor(){
        return $this->belongsTo(\Modules\Vendors\Entities\Vendors::class,'vendor_id');
    }
    
}
