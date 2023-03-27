<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CoreSeeder extends Seeder{

    public function run(){

        $core_currencies = [
            ['id' => 'QR ', 'name' => 'ريال قطري', 'symbol' => 'QR '],
        ];

        foreach($core_currencies as $row){
            $record = new \Modules\Core\Entities\Currency;
            $record->id = $row['id'];
            $record->name = $row['name'];
            $record->symbol = $row['symbol'];
            $record->save();
        }

        $core_countries = [
            [ 'name_en' => 'َQatar', 'name_ar' => 'قطر','created_by' =>1],
          
        ];

        foreach($core_countries as $row){
            $record = new \Modules\Core\Entities\Country;
            $record
            ->setTranslation('name', 'en',  $row['name_en'])
            ->setTranslation('name', 'ar',  $row['name_ar']);
            $record->created_by = $row['created_by'];
            $record->save();
        }

        $core_country_provinces = [
            [ 'country_id' => 1,'name' => 'Ad Dawhah ', 'name_ar' => 'الدوحة','full_name' => 'Ad Dawhah ', 'created_by' =>1],
            [ 'country_id' => 1,'name' => 'Al Ghuwariyah', 'name_ar' => 'الغوارية','full_name' => 'Al Ghuwariyah', 'created_by' =>1],
            [ 'country_id' => 1,'name' => 'Al Jumaliyah', 'name_ar' => 'الجمالية','full_name' => 'Al Jumaliyah', 'created_by' =>1],
            [ 'country_id' => 1,'name' => 'Al Khawr','name_ar' => 'الخور', 'full_name' => 'Al Khawr', 'created_by' =>1],
            [ 'country_id' => 1,'name' => 'Al Wakrah','name_ar' => 'الوكرة', 'full_name' => 'Al Wakrah', 'created_by' =>1],
            [ 'country_id' => 1,'name' => 'Ar Rayyan','name_ar' => 'الريان', 'full_name' => 'Ar Rayyan', 'created_by' =>1],
            [ 'country_id' => 1,'name' => 'Jariyan al Batnah','name_ar' => 'جريان الباطنة', 'full_name' => 'Jariyan al Batnah', 'created_by' =>1],
            [ 'country_id' => 1,'name' => 'Ash Shamal', 'name_ar' => 'الشمال','full_name' => 'Ash Shamal', 'created_by' =>1],
            [ 'country_id' => 1,'name' => 'Umm Salal','name_ar' => 'أم صلال', 'full_name' => 'Umm Salal', 'created_by' =>1],
            [ 'country_id' => 1,'name' => 'Mesaieed', 'name_ar' => 'مسيعيد','full_name' => 'Mesaieed', 'created_by' =>1],
        ];

        foreach($core_country_provinces as $row){
            $record = new \Modules\Core\Entities\CountryProvince;
            $record->country_id = $row['country_id'];
            $record->setTranslation('name', 'en',  $row['name'])
            ->setTranslation('name', 'ar',  $row['name_ar']);
            $record->full_name = $row['full_name'];
            $record->created_by = $row['created_by'];
            $record->save();
        }

        $cms_social_media_links = [
            [ 'type_en' => 'FaceBook', 'type_ar' => 'فيس بوك','content' => 'https://www.facebook.com/mohammed.3baid/','created_by' =>1],
            [ 'type_en' => 'Instagram', 'type_ar' => 'انستحرام','content' => 'https://www.instagram.com/mhmd3baid/','created_by' =>1],
            [ 'type_en' => 'Linked In', 'type_ar' => 'لينكد ان','content' => 'https://www.linkedin.com/in/mohammad-obaid-3b4538201/','created_by' =>1],
            [ 'type_en' => 'YouTube In', 'type_ar' => 'يوتيوب','content' => 'https://www.youtube.com/','created_by' =>1],
          
        ];
        foreach($cms_social_media_links as $row){
            $record = new \Modules\CMS\Entities\SocialMediaLink();
            $record->setTranslation('type', 'en',  $row['type_en'])
            ->setTranslation('type', 'ar',  $row['type_ar']);
            $record->content = $row['content'];
            $record->created_by = $row['created_by'];
            $record->save();
        }
        $cms_terms = [
            [ 'type_en' => 'about_us', 'type_ar' => 'عنا','content_en' => 'yes','content_ar' => 'نعم','created_by' =>1],
            [ 'type_en' => 'polices', 'type_ar' => 'سياسات الخصوصية','content_en' => 'yes','content_ar' => 'نعم','created_by' =>1],
            [ 'type_en' => 'terms', 'type_ar' => 'البنود','content_en' => 'yes','content_ar' => 'نعم','created_by' =>1],
          
        ];
        foreach($cms_terms as $row){
            $record = new \Modules\CMS\Entities\Term();
            $record->setTranslation('type', 'en',  $row['type_en'])
            ->setTranslation('type', 'ar',  $row['type_ar']);
            $record->setTranslation('content', 'en',  $row['content_en'])
            ->setTranslation('content', 'ar',  $row['content_ar']);
            $record->created_by = $row['created_by'];
            $record->save();
        }
        $vn_coupons_types = [
            [ 'name_en' => 'percentage', 'name_ar' => 'نسبة'],
            [ 'name_en' => 'fixed amount', 'name_ar' => 'مبلغ معين'],
            [ 'name_en' => 'free shipping', 'name_ar' => 'شحن مجاني'],
          
        ];
        foreach($vn_coupons_types as $row){
            $record = new \Modules\Vendors\Entities\CouponsType();
            $record->setTranslation('name', 'en',  $row['name_en'])
            ->setTranslation('name', 'ar',  $row['name_ar']);
            $record->save();
        }
    }
}
