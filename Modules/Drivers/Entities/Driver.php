<?php

namespace Modules\Drivers\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Driver extends Model implements HasMedia{
    use SoftDeletes;
    use InteractsWithMedia;

    protected $table = 'dr_drivers';
    protected $appends = ['license_image_url','person_image_url'];
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
    protected static function boot(){
        parent::boot();
        static::addGlobalScope(new \App\Scopes\ActiveScopeForDriver);
    }
    public function scopeWhereActive($query){
        return $query->where('status_id', 1);
    }
    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media  $media = null): void{
        $this->addMediaConversion('thumb')
              ->width(400)
              ->height(400);
    }
    public function image(){
        return $this->morphOne(Media::class,'model');
    }
   
    public function getLicenseImageUrlAttribute(){
        $image = \Spatie\MediaLibrary\MediaCollections\Models\Media::
        where('collection_name', 'driver-license-image')
        ->where('model_id', $this->id)
        ->where('model_type', 'Modules\Drivers\Entities\Driver')
        ->latest()
        ->first();
        if($image){
            return url('/') . '/storage/app/public/' . $image->id . '/' . $image->file_name;
        }

        return asset('/public/assets/images/avatars/avatar6.png');

    }
    public function getPersonImageUrlAttribute(){
        $image = \Spatie\MediaLibrary\MediaCollections\Models\Media::
        where('collection_name', 'driver-image')
        ->where('model_id', $this->id)
        ->where('model_type', 'Modules\Drivers\Entities\Driver')
        ->latest()
        ->first();

        if($image){
            return url('/') . '/storage/app/public/' . $image->id . '/' . $image->file_name;
        }

        return asset('/public/assets/images/avatars/avatar6.png');

    }
    public function created_by_user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class, 'created_by');
    }
    public function user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class);
    }
    public function type_of_driver(){
        return $this->belongsTo(\Modules\Drivers\Entities\DriverType::class, 'type_id');
    }
    public function status(){
        return $this->belongsTo(\Modules\Drivers\Entities\DriverStatus::class);
    }
    public function order_state(){
        return $this->hasMany(\Modules\Products\Entities\DriverOrderState::class);
    }
}
