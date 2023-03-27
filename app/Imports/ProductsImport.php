<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow{
    protected  $data;

   

    public function __construct($data){
        $this->data = $data;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row){
        return new \Modules\Products\Entities\Product([
            'product_code' => $row['code'],
            'name' => [
                'en' => $row['name_en'],
                'ar' => $row['name_ar']
            ],
            'description' => [
                'en' => $row['description_en'],
                'ar' => $row['description_ar']
            ],
            'category_id' => $this->data->category_id,
            'vendor_id' => $this->data->vendor_id,
            'status_id' => $this->data->status_id ? $this->data->status_id : '2',
            'currency_id' => 'QR',
            'price' => $row['price'],
            'created_by' => \Auth::user()->id,
        ]);
    }
}
