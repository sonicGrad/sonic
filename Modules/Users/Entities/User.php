<?php

namespace Modules\Users\Entities;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements JWTSubject, HasMedia{
    use  HasRoles, Notifiable;
    use SoftDeletes;
    use InteractsWithMedia;

    // use HasApiTokens, HasFactory, Notifiable;
    protected static function boot(){
        parent::boot();

        static::addGlobalScope(new \App\Scopes\OrderByScope);
        // static::addGlobalScope(new \App\Scopes\ActiveStatusForLoginScope);
    }
    protected $table = 'um_users';
    protected $appends = ['person_image_url'];
    public $translatable = ['name'];
    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media  $media = null): void{
        $this->addMediaConversion('thumb')
              ->width(400)
              ->height(400);
    }
    public function image(){
        return $this->morphOne(Media::class,'model');
    }
    public function getPersonImageUrlAttribute(){
        $image = \Spatie\MediaLibrary\MediaCollections\Models\Media::
        where('collection_name', 'user-image')
        ->where('model_id', $this->id)
        ->where('model_type', 'Modules\Users\Entities\User')
        ->latest()
        ->first();

        if($image){
            return url('/') . '/storage/app/public/' . $image->id . '/' . $image->file_name;
        }

        return asset('/public/assets/images/avatars/avatar6.png');

    }
    public function getJWTIdentifier(){
        return $this->getKey();
    }
    public function getJWTCustomClaims(){
        return [];
    }


    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d H:i:s a'
    ];

    public function authorize($permission, $type = "json"){
        if(\Auth::user()->can(trim($permission))){
            return true;
        }

        abort(403);
    }

    public function province(){
        return $this->belongsTo(\Modules\Core\Entities\CountryProvince::class);
    }
    public function status(){
        return $this->belongsTo(\Modules\Users\Entities\UserStatus::class);
    }

    public function created_by(){
        return $this->belongsTo(\Modules\Users\Entities\User::class, 'created_by');
    }
    public function scopeWhereCreatedAt($query, $created_at){
        return $query->where(function($query) use ($created_at){
            if(str_contains(trim($created_at), ' to ')){
                $created_at = explode(' to ', $created_at);
                $created_at_from = $created_at[0];
                $created_at_from = $created_at[1];

                $query->whereDate('created_at', '>=', date('Y-m-d', strtotime(trim($created_at[0]))));
                $query->whereDate('created_at', '<=', date('Y-m-d', strtotime(trim($created_at[1]))));
            }else{
                $query->whereDate('created_at', date('Y-m-d', strtotime(trim($created_at))));
            }
        });
    }
    public function buyerable(){
        return $this->morphOne(\Modules\Products\Entities\Orders::class, 'buyerable');
    }
    public function driver(){
        return $this->hasOne(\Modules\Drivers\Entities\Driver::class, 'user_id');
    }
    public function rate(){
        return $this->hasMany(\Modules\Users\Entities\Rate::class, 'grantee_id');
    }
    public function favorite(){
        return $this->belongsToMany(\Modules\Products\Entities\Product::class, 'um_favorites');
    }

    public function coupons(){
        return $this->belongsToMany(\Modules\Vendors\Entities\Coupon::class, 'um_user_coupons');
    }
    public function ratings(){
        return $this->morphMany(\Modules\Users\Entities\Rate::class, 'rateable', 'rateable_type', 'rateable_id', 'id');
    }public function reasons(){
        return $this->morphMany(\Modules\Users\Entities\Reason::class, 'reasonable', 'reasonable_type', 'reasonable_id', 'id');
    }

    public function address_book(){
       return $this->hasMany(\Modules\Users\Entities\AddressBook::class);
    }
    public function vendor(){
        return $this->hasOne(\Modules\Vendors\Entities\Vendors::class);
    }

    public function routeNotificationForTwilio(){
        return '+970594034429';
    }
}
