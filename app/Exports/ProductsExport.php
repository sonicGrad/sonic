<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductsExport implements FromView{

    protected $data;
    public function __construct($data){
        $this->data = $data;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('products::exports.products', [
            'products' => \Modules\Products\Entities\Product::with('created_by_user', 'vendor','category.type_of_vendor')
            ->when($this->data->vendor_id, function($q){
                $q->where('vendor_id', $this->data->vendor_id);
            })
            ->when($this->data->category_id, function($q){
                $q->where('category_id', $this->data->category_id);
            })
            ->when($this->data->type_id, function($q){
                $q->whereHas('vendor.type_of_vendor', function($eloquent){
                    $eloquent->whereId(trim($this->data->type_id));
                });
            })
            ->get()
        ]);
    }
}
