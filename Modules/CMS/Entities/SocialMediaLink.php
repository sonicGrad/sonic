<?php

namespace Modules\CMS\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class SocialMediaLink extends Model{
    use HasTranslations;
    use SoftDeletes;

    protected $table = 'cms_social_media_links';
    public function created_by_user(){
        return $this->belongsTo(\Modules\Users\Entities\User::class, 'created_by');
    }
   
    public $translatable = ['type'];
    
    protected $casts = ['created_at' => 'datetime:Y-m-d H:i:s a'];
}
