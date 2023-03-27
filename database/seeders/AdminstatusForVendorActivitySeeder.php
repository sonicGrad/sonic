<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminstatusForVendorActivitySeeder extends Seeder{

    public function run(){
        
        $admin_status_for_vendor_activities = [
            [
                // 'name_en' => 'active','name_ar' => 'فعال',
                // 'name_en' => 'pending','name_ar' => 'قيد المتابعة'
                'name_en' => 'reject','name_ar' => 'مرفوض'
                
            ],
        ];

        foreach($admin_status_for_vendor_activities as $row){
            $record = new \Modules\Users\Entities\AdminStatusForVendorActivity;
            $record
            ->setTranslation('name', 'en',  $row['name_en'])
            ->setTranslation('name', 'ar',  $row['name_ar']);
            $record->created_by = '1';
            $record->save();
        }

    }
}