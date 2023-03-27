<?php

namespace Modules\Products\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;
use Maatwebsite\Excel\Facades\Excel;

class ProductsController extends Controller{
    public function __construct(){
        $this->middleware(['auth']);
    }
    public function index(){
        $data = \Modules\Products\Entities\Product::get();
        return response()->json($data);
    }
    public function manage(Request $request){
        \Auth::user()->authorize('products_module_products_manage');

        $data['activePage'] = ['products' => 'products'];
        $data['breadcrumb'] = [
            ['title' => 'Products'],
        ];
        $data['addRecord'] = ['href' => route('products.create')];
        if ($request->ajax()) {
            $user= \Auth::user();
            if($user->hasRole('vendor')){
                $vendorsParent = \Modules\Vendors\Entities\Vendors::with('children')->where('user_id', $user->id)->first();
                $vendor_ids = $vendorsParent->children()->pluck('id');
                $vendor_ids->push($vendorsParent->id);
                $data = \Modules\Products\Entities\Product
                ::with('created_by_user', 'vendor','category.type_of_vendor','status','admin_status')
                ->whereIn('vendor_id', $vendor_ids)
                ->select('*');
            }else{
                $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
                $data = \Modules\Products\Entities\Product
                ::with('created_by_user', 'vendor','category.type_of_vendor','status','admin_status')
                ->select('*');
            }
            return Datatables::of($data)
            ->addColumn('action', function ($product) {
                $user= \Auth::user();
                $btn = '';
                if($user->hasRole('super_admin')){
                    $btn .= '<button type="button" data-action="changeState" class="btn btn-sm btn-danger  "  data-toggle="modal" data-target="#changeStatus' . $product->id . '">';
                    // $btn .= '<i class="fa-solid fa-eye"></i>';
                    $btn .= "change Status";
                    $btn .= '</button>';
                    $btn .= '<div class="modal fade"  id="changeStatus'. $product->id. '" tabindex="-1" role="dialog" aria-labelledby="changeStatus'. $product->id.'" aria-hidden="true">';
                    $btn .= '  <div class="modal-dialog" role="document">';
                    $btn .= '<div class="modal-content" >';
                    $btn .= '<div class="modal-header">';
                    $btn .= '<h5 class="modal-title" id="">change Status For Admin</h5>';
                    $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                    $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                    $btn .= '<div class="modal-body" style="height: 100px;">';
                    $btn .= '<div class="form-group">';
                    $btn .= '<label  class="col-sm-2 control-label" for="" >Admin Status</label>';
                    $btn .= '<div class="col-sm-10">';
                    $btn .= '<select name="admin_status" id="admin_status data-action="change" class="form-control ">';
                    $btn .= '<option value="">Choose Status...</option>';
                    foreach (\Modules\Users\Entities\AdminStatusForVendorActivity::get() as $admin_status){
    
                        $btn .= '<option data-id="'. $product->id. '" value="';
                       $btn .= $admin_status->id;
                       $btn .= '"';
                       if($admin_status->id   == $product->admin_status){
                           $btn  .= 'selected';
                       }
                       $btn .= '>';
                       $btn .= $admin_status->name ;
                       $btn .= '</option>' ;
                       
                    }
                       $btn .= '</select>';
                    $btn .= '<p class="invalid-feedback"></p>';
                    $btn .= '</div>';
                    $btn .= ' </div>';
                
            
       
                    $btn .= '</div>';
                    $btn .= ' <div class="modal-footer"><button type="button" data-id="'. $product->id. '" data-action="save-new-status" class="btn btn-primary">Save changes</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                    $btn .= '</div></div></div></div>';
                }
                $btn .= '<a data-action="edit" href=' .route('products.edit', $product->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
                $btn .= '<a data-action="destroy" data-id='. $product->id. '  class="btn btn-xs red p-2  tooltips"><i class="fa fa-times" aria-hidden="true"></i> </a>';
                return $btn;
            })
            ->addColumn('modalEn', function ($product) {
                $btn = '';
                $btn .= '<button type="button" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#descEn' . $product->id . '">';
                $btn .= "Show";
                $btn .= '</button>';
                $btn .= '<div class="modal fade" id="descEn'. $product->id. '" tabindex="-1" role="dialog" aria-labelledby="descEn'. $product->id.'" aria-hidden="true">';
                $btn .= '  <div class="modal-dialog" role="document">';
                $btn .= '<div class="modal-content">';
                $btn .= '<div class="modal-header">';
                $btn .= '<h5 class="modal-title" id="">Description</h5>';
                $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                $btn .= '<div class="modal-body">';
                $btn .=$product->getTranslations('description')['en'];
                $btn .= '</div>';
                $btn .= ' <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                $btn .= '</div></div></div></div>';
                return $btn;
            })
            ->addColumn('modalAr', function ($product) {
                $btn = '';
                $btn .= '<button type="button" class="btn 
                 btn-primary"  data-toggle="modal" data-target="#descAr' . $product->id . '">';
                $btn .= "Show";

                $btn .= '</button>';
                $btn .= '<div class="modal fade" id="descAr'. $product->id. '" tabindex="-1" role="dialog" aria-labelledby="descAr'. $product->id.'" aria-hidden="true">';
                $btn .= '  <div class="modal-dialog" role="document">';
                $btn .= '<div class="modal-content">';
                $btn .= '<div class="modal-header">';
                $btn .= '<h5 class="modal-title" id="">Description</h5>';
                $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                $btn .= '<div class="modal-body">';
                $btn .= $product->getTranslations('description')['ar'];
                $btn .= '</div>';
                $btn .= ' <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                $btn .= '</div></div></div></div>';
                return $btn;
            })
            ->editColumn('status_id',function($row){
                $btn = view('core.status_label')->with(['row'=>$row])->render();
                return $btn;
            })
            ->editColumn('admin_status',function($row){
                $btn = view('core.status_label')->with(['row'=>$row])->render();
                return $btn;
            })
            ->rawColumns(['action', 'modalEn' ,'modalAr','status_id','admin_status'])
            ->filter(function ($query) use ($request) {
                if ($request->has('name_ar') && $request->get('name_ar') != null) {
                    $query->where('name->ar', 'like', "%{$request->get('name_ar')}%");
                }
                if ($request->has('name_en') && $request->get('name_en') != null) {
                    $query->where('name->en', 'like', "%{$request->get('name_en')}%");
                }
                if ($request->has('type_id') && $request->get('type_id') != null) {
                    $query->whereHas('vendor.type_of_vendor', function($eloquent) use ($request){
                        $eloquent->whereId(trim($request->get('type_id')));
                    });
                }
                if ($request->has('vendor_id') && $request->get('vendor_id') != null) {
                    $query->where('vendor_id', trim($request->get('vendor_id')));
                }
                if ($request->has('category_id') && $request->get('category_id') != null) {
                    $query->where('category_id', trim($request->get('category_id')));
                }
                if ($request->has('status_id') && $request->get('status_id') != null) {
                    $query->where('status_id', trim($request->get('status_id')));
                }
                if ($request->has('admin_status') && $request->get('admin_status') != null) {
                    $query->where('admin_status', trim($request->get('admin_status')));
                }

            })
            ->toJson();
        }

        return view('products::products',[
            'data' => $data,
            'types' => \Modules\Vendors\Entities\TypeOFVendor::get(),
            'vendors' => \Modules\Vendors\Entities\Vendors::get(),
            'categories' => \Modules\Products\Entities\Category::whereNull('parent_id')->get(),
            'adminStatues' => \Modules\Users\Entities\AdminStatusForVendorActivity::get(),
            'statues' => \Modules\Products\Entities\ProductStatus::get()
        ]);
    }
    public function manage1(Request $request){
        \Auth::user()->authorize('products_module_products_manage');

        $data['activePage'] = ['products' => 'products'];
        $data['breadcrumb'] = [
            ['title' => 'Products'],
        ];
        $data['addRecord'] = ['href' => route('products.create')];
        // return \Modules\Products\Entities\Product::with('created_by_user', 'vendor','category.type_of_vendor->toSql();
        if ($request->ajax()) {
            $user= \Auth::user();
            if($user->hasRole('vendor')){
                $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
                $data = \Modules\Products\Entities\Product
                ::with('created_by_user', 'vendor','category.type_of_vendor','status','admin_status')
                ->where('vendor_id', $vendor->id)
                ->where('admin_status', '2')
                ->select('*');
            }else{
                $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
                $data = \Modules\Products\Entities\Product
                ::with('created_by_user', 'vendor','category.type_of_vendor','status','admin_status')
                ->where('admin_status', '2')
                ->select('*');
            }
            return Datatables::of($data)
            ->addColumn('action', function ($product) {
               
            })
            ->addColumn('modalEn', function ($product) {
                $btn = '';
                $btn .= '<button type="button" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#descEn' . $product->id . '">';
                // $btn .= '<i class="fa-solid fa-eye"></i>';
                $btn .= "Show";
                $btn .= '</button>';
                $btn .= '<div class="modal fade"  id="descEn'. $product->id. '" tabindex="-1" role="dialog" aria-labelledby="descEn'. $product->id.'" aria-hidden="true">';
                $btn .= '  <div class="modal-dialog" role="document">';
                $btn .= '<div class="modal-content" >';
                $btn .= '<div class="modal-header">';
                $btn .= '<h5 class="modal-title" id="">Description</h5>';
                $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                $btn .= '<div class="modal-body">';
                $btn .=$product->getTranslations('description')['en'];
                $btn .= '</div>';
                $btn .= ' <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                $btn .= '</div></div></div></div>';
                return $btn;
            })
            ->addColumn('modalAr', function ($product) {
                $btn = '';
                $btn .= '<button type="button" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#descAr' . $product->id . '">';
                $btn .= "Show";

                $btn .= '</button>';
                $btn .= '<div class="modal fade" id="descAr'. $product->id. '" tabindex="-1" role="dialog" aria-labelledby="descAr'. $product->id.'" aria-hidden="true">';
                $btn .= '  <div class="modal-dialog" role="document">';
                $btn .= '<div class="modal-content">';
                $btn .= '<div class="modal-header">';
                $btn .= '<h5 class="modal-title" id="">Description</h5>';
                $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                $btn .= '<div class="modal-body">';
                $btn .= $product->getTranslations('description')['ar'];
                $btn .= '</div>';
                $btn .= ' <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                $btn .= '</div></div></div></div>';
                return $btn;
            })
            ->rawColumns(['action', 'modalEn' ,'modalAr'])
            ->filter(function ($query) use ($request) {
                if ($request->has('name_ar') && $request->get('name_ar') != null) {
                    $query->where('name->ar', 'like', "%{$request->get('name_ar')}%");
                }
                if ($request->has('name_en') && $request->get('name_en') != null) {
                    $query->where('name->en', 'like', "%{$request->get('name_en')}%");
                }
                if ($request->has('type_id') && $request->get('type_id') != null) {
                    $query->whereHas('vendor.type_of_vendor', function($eloquent) use ($request){
                        $eloquent->whereId(trim($request->get('type_id')));
                    });
                }
                if ($request->has('vendor_id') && $request->get('vendor_id') != null) {
                    $query->where('vendor_id', trim($request->get('vendor_id')));
                }
                if ($request->has('category_id') && $request->get('category_id') != null) {
                    $query->where('category_id', trim($request->get('category_id')));
                }

            })
            ->toJson();
        }

        return view('products::pending-products',[
            'data' => $data,
            'types' => \Modules\Vendors\Entities\TypeOFVendor::get(),
            'vendors' => \Modules\Vendors\Entities\Vendors::get(),
            'categories' => \Modules\Products\Entities\Category::whereNull('parent_id')->get(),
        ]);
    }
    public function create(){
        \Auth::user()->authorize('products_module_products_manage');
        $user =\Auth::user();
        $data['activePage'] = ['products' => 'products'];
        $data['breadcrumb'] = [
            ['title' => 'Products'],
            ['title' => 'Add Product'],
        ];
        if($user->hasRole('vendor')){
            $user = \Auth::user();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            $vendorsParent = \Modules\Vendors\Entities\Vendors::with('children')->where('user_id', $user->id)->first();
            $vendor_ids = $vendorsParent->children()->pluck('id');
            $vendor_ids->push($vendorsParent->id);
            $branches = \Modules\Vendors\Entities\Vendors::with('user.province')->whereIn('id', $vendor_ids)->get();
            return view('products::create-product-by-vendor',[
                'data' => $data,
                'categories' => \Modules\Products\Entities\Category::where('vendor_type', $vendor->type_id)->get(),
                'branches'=> $branches,
                'vendor_type' => $vendor->type_id,
            ]);
        }
        return view('products::create-product',[
            'data' => $data,
            'types' => \Modules\Vendors\Entities\TypeOFVendor::get(),
            'vendors' => \Modules\Vendors\Entities\Vendors::get(),
            'types_of_features' => \Modules\Core\Entities\TypeOfFeature::get(),
        ]);
    }
    public function edit($id){
        \Auth::user()->authorize('products_module_products_manage');

        $product = \Modules\Products\Entities\Product::with('created_by_user', 'vendor','category','type')
        ->whereId($id)->first();
        $data['activePage'] = ['products' => 'products'];
        $data['breadcrumb'] = [
            ['title' => 'Categories'],
            ['title' => 'Edit Product Info'],
        ];
        // return $product ;
        $vendor = \Modules\Vendors\Entities\Vendors::with('type')->whereId($product->vendor_id)->first();
        $user = \Auth::user();
        $variation_attributes = \Modules\Products\Entities\CategoryAttributeType::
        where('attribute_type_id', '<>','1')
        ->with('attribute')
        ->where('category_id', $vendor->type_id)
        ->get();

        $attributesCollect = collect([]);
            $attributesCollectParent = collect([]);
            $attributes = \Modules\Products\Entities\ProductVariation::with('attributes')
            ->whereHas('attributes', function($q){
                $q->where('type_id', '<>', '1');            })
            ->Where('product_id', $product->id)->get();
            foreach($attributes as $attribute){
                foreach( $attribute->attributes as $item){
                    $list = \Modules\Products\Entities\AttributeTypeValue::where('attribute_type_id', $item->type_id)->get('name');
                    if($list){
                        $attributesCollect->push([
                            'id' => $item->id,
                            'variation_id' => $item->variation_id,
                            'type_id' => $item->type_id,
                            'value' => $item->value,
                            'quantity' => $attribute->quantity,
                            'price' => $attribute->price,
                            'list' => $list
                        ]);
                    }
                }
                $attributesCollectParent->push($attributesCollect);
                $attributesCollect = collect([]);
            }
            $DefaultAttribute = \Modules\Products\Entities\VariationAttribute::with('variation')->where('type_id', '1')
            ->whereHas('variation.product', function($q) use($product){
                $q->where('id', $product->id);
            })
            ->first();
        if($user->hasRole('vendor')){
            $user = \Auth::user();
            $vendorsParent = \Modules\Vendors\Entities\Vendors::with('children')->where('user_id', $user->id)->first();
            $vendor_ids = $vendorsParent->children()->pluck('id');
            $vendor_ids->push($vendorsParent->id);
            $branches = \Modules\Vendors\Entities\Vendors::with('user.province')->whereIn('id', $vendor_ids)->get();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            $parent = \Modules\Vendors\Entities\Vendors::where('user_id', '<>', \Auth::user()->id)->where('parent_id',$vendor->parent_id)->first();
            if($parent->user_id == $user->id){
                return response()->json(['message' => 'Not Allow'], 403);
            }
            if( !in_array($product->vendor_id,$vendor_ids->toArray())){
               abort(403);            
            }
            
            // return $attributesCollectParent[0][0]['list'][0]->name;
            // return $attributesCollectParent;
            // return $attributesCollect[0]['list'][0]->name;
            
            return view('products::edit-product-by-vendor',[
                'data' => $data,
                'product' => $product,
                'categories' => \Modules\Products\Entities\Category::where('vendor_type', $vendor->type_id)->get(),
                'statuses' => \Modules\Products\Entities\ProductStatus::get(),
                'branches' => $branches,
                'attributes' => $attributesCollectParent,
                'variation_attributes' => $variation_attributes,
                'DefaultAttribute' => $DefaultAttribute
            ]);
        }
        // return $attributesCollectParent;

        return view('products::edit-product',[
            'data' => $data,
            'product' => $product,
            'types' => \Modules\Vendors\Entities\TypeOFVendor::get(),
            'vendors' => \Modules\Vendors\Entities\Vendors::get(),
            'categories' => \Modules\Products\Entities\Category::whereNull('parent_id')->get(),
            'types_of_features' => \Modules\Core\Entities\TypeOfFeature::get(),
            'statuses' => \Modules\Products\Entities\ProductStatus::get(),
            'admin_statuses' => \Modules\Users\Entities\AdminStatusForVendorActivity::get(),
            'attributes' => $attributesCollectParent,
            'variation_attributes' => $variation_attributes,
            'DefaultAttribute' => $DefaultAttribute,
            'vendor' => $vendor
        ]);
    }
    public function show($id){
        $product = \Modules\Products\Entities\Product::whereId($id)->first();
        $images = $product->getMedia('product-image');
        $images_new  = collect([]);
        foreach($images as $image){
            $new['url'] =  url('/') . '/storage/app/public/' . $image->id . '/' . $image->file_name;
            $new['name'] = $image->file_name;
            $images_new->push($new);
        }
        return response()->json($images_new,200);
    }
    public function storeForVendor(Request $request){
        \Auth::user()->authorize('products_module_products_store');
        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'product_code' => 'required',
            'category_id' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $user = \Auth::user();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            if(!$vendor){
                return response()->json(['message' =>'Not Allowed'],403); 
            }
            $branch = null;
            if($user->hasRole('vendor') && $user->hasRole('main branch supplier')){
                if(!$request->branch_id){
                    return response()->json(['message' =>'Not Allowed You Should Add Branch'],403); 
                }
                $branch = \Modules\Vendors\Entities\Vendors::where('id',$request->branch_id)->first();
                if(!$branch){
                    $branch = \Modules\Vendors\Entities\Vendors::
                    where('id',$request->branch_id)
                    ->where('parent_id', $vendor->id)
                    ->first();
                    
                    return response()->json([$vendor->id],403); 
                    if(!$branch){
                        return response()->json(['message' =>'Not Allowed'],403); 
                    }
                }

            }
            $product = \Modules\Products\Entities\Product::where('vendor_id' , $vendor->id)
            ->where('product_code', $request->product_cod)->first();
            if($product){
                return response()->json(['message' =>'This Product Are Already Exisit'],403);
            }
            $product = new \Modules\Products\Entities\Product;
            $product
            ->setTranslation('name', 'en', $request->name_en)
            ->setTranslation('name', 'ar',  $request->name_ar);

            $product
            ->setTranslation('description', 'en', $request->description_en)
            ->setTranslation('description', 'ar',  $request->description_ar);
            $product->category_id = $request->category_id;
            $branch != null ? $product->vendor_id = $branch->id : $product->vendor_id = $vendor->id;
            // $product->price = $request->price;
            // $product->quantity = $request->quantity;
            // $product->currency_id = 'QR';
            $product->product_code = $request->product_code;
            $product->status_id = '1' ;
            $user->hasRole('trust_vendor') ? $product->admin_status  = '1' : $product->admin_status  = '2';
            $product->created_by = \Auth::user()->id;
            $product->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok', 'data'=> $product]);

    }
    public function updateForVendor(Request $request, $id){
        \Auth::user()->authorize('products_module_products_store');
        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'product_code' => 'required',
            'category_id' => 'required',

        ]);
        \DB::beginTransaction();
        try {
            $user = \Auth::user();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
           
            if(!$vendor){
                return response()->json(['message' =>'Not Allowed'],403); 
            }
            $branch = null;
            if($user->hasRole('vendor') && $user->hasRole('main branch supplier')){
                if(!$request->branch_id){
                    return response()->json(['message' =>'Not Allowed You Should Add Branch'],403); 
                }
                $branch = \Modules\Vendors\Entities\Vendors::
                Where('id',$request->branch_id)
                ->where('id', $vendor->id)
                ->orWhere('parent_id', $vendor->id)
                ->first();
                if(!$branch){
                    return response()->json(['message' =>'Not Allowed'],403); 
                }

            }
            $product = \Modules\Products\Entities\Product::
            where('id', '<>', $id)->where('vendor_id' , $request->vendor_id)
            ->where('product_code', $request->product_code)->first();
            if($product){
                return response()->json(['message' =>'This Product Are Already Exisit'],403);
            }
            if($product){
                return response()->json(['message' =>'This Product Are Already Exisit'],403);
            }
            $product =  \Modules\Products\Entities\Product::whereId($id)->first();
            $product
            ->setTranslation('name', 'en', $request->name_en)
            ->setTranslation('name', 'ar',  $request->name_ar);
            
            $product
            ->setTranslation('description', 'en', $request->description_en)
            ->setTranslation('description', 'ar',  $request->description_ar);
            $product->category_id = $request->category_id;
            $branch != null ? $product->vendor_id = $branch->id : $product->vendor_id = $product->vendor_id;
            // $product->price = $request->price;
            // $product->quantity = $request->quantity;
            $product->product_code = $request->product_code;
            $product->currency_id = 'QR';
            $product->status_id = $request->status_id;
            $product->created_by = \Auth::user()->id;
            $product->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }
        
        return response()->json(['message' => 'ok', 'data'=> $product]);

    }
    public function store(Request $request){
        \Auth::user()->authorize('products_module_products_store');
        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'type_id' => 'required',
            'vendor_id' => 'required',
            'product_code' => 'required',
        ]);
        \DB::beginTransaction();
        try {

            $product = \Modules\Products\Entities\Product::where('vendor_id' , $request->vendor_id)
            ->where('product_code', $request->product_cod)->first();
            if($product){
                return response()->json(['message' =>'This Product Are Already Exisit'],403);
            }
            $product = new \Modules\Products\Entities\Product;
            $product
            ->setTranslation('name', 'en', $request->name_en)
            ->setTranslation('name', 'ar',  $request->name_ar);

            $product
            ->setTranslation('description', 'en', $request->description_en)
            ->setTranslation('description', 'ar',  $request->description_ar);
            $product->category_id = $request->category_id;
            $product->vendor_id = $request->vendor_id;
            $product->product_code = $request->product_code;
            $product->currency_id = 'QR';
            $product->created_by = \Auth::user()->id;
            
            $product->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }
        
        return response()->json(['message' => 'ok', 'data'=> $product]);

    }
    public function update(Request $request, $id){
        \Auth::user()->authorize('products_module_products_update');
        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'type_id' => 'required',
            'vendor_id' => 'required',
            'product_code' => 'required',
        ]);
        \DB::beginTransaction();
        try {

            $product = \Modules\Products\Entities\Product::where('id', '<>', $id)
            ->where('vendor_id' , $request->vendor_id)
            ->where('product_code', $request->product_code)
            ->first();
            if($product){
                return response()->json(['message' =>'This Product Are Already Exisit'],403);
            }
            $vendor = \Modules\Vendors\Entities\Vendors::whereId($request->vendor_id)->first();
            if($vendor->type_id  != $request->type_id){
                return response()->json(['message' =>'Data Not Correct when Vendor not related to type'],403);

            }
            $product =  \Modules\Products\Entities\Product::whereId($id)->first();
            $product
            ->setTranslation('name', 'en', $request->name_en)
            ->setTranslation('name', 'ar',  $request->name_ar);

            $product
            ->setTranslation('description', 'en', $request->description_en)
            ->setTranslation('description', 'ar',  $request->description_ar);
            $product->category_id = $request->category_id;
            $product->vendor_id = $request->vendor_id;
            $product->product_code = $request->product_code;
            $product->status_id = $request->status_id;
            $product->admin_status = $request->admin_status;
            $request->currency_id = 'QR';
            $product->created_by = \Auth::user()->id;
            $product->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok','data' => $product]);

    }
    public function destroy(Request $request, $id){
        \Auth::user()->authorize('products_module_products_destroy');

        $product = \Modules\Products\Entities\Product::whereId($id)->first();
        $product->delete();
        return response()->json('Ok',200);
    }
    public function addImage(Request $request){
        $id = $request->userId;
        $product = \Modules\Products\Entities\Product::whereId($id)->first();
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $extension = strtolower($request->file('file')->extension());
            $media_new_name = strtolower(md5(time())) . "." . $extension;
            $collection = "product-image";
            
            $product->addMediaFromRequest('file')
                ->usingFileName($media_new_name)
                ->usingName($request->file('file')->getClientOriginalName())
                ->toMediaCollection($collection);
            return response()->json(['message' => 'ok']);
        }
    }

    public function removeImage(Request $request, $id){
        $image = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('file_name', $id)->first();
        $image->delete();
        return response()->json(['message' => 'ok']);
    }

    public function createByExcel(){
        \Auth::user()->authorize('products_module_products_manage');

        $data['activePage'] = ['products' => 'products'];
        $data['breadcrumb'] = [
            ['title' => 'Products'],
            ['title' => 'Add Product By Importing Excel'],
        ];
        $user = \Auth::user();
        if($user->hasRole('vendor')){
            $vendorsParent = \Modules\Vendors\Entities\Vendors::with('children')->where('user_id', $user->id)->first();
            $vendor_ids = $vendorsParent->children()->pluck('id');
            $vendor_ids->push($vendorsParent->id);
            $branches =\Modules\Vendors\Entities\Vendors::with('user.province')->whereIn('id', $vendor_ids)->get();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            return view('products::create-products-for-vendor-by-pdf',[
                'data' => $data,
                'categories' => \Modules\Products\Entities\Category::where('vendor_type', $vendor->type_id)->get(),
                'branches' => $branches
            ]);
        }
        return view('products::create-products-by-pdf',[
            'data' => $data,
            'types' => \Modules\Vendors\Entities\TypeOFVendor::get(),
            'vendors' => \Modules\Vendors\Entities\Vendors::get(),

        ]);
    }
    public function exportExcel(){
        \Auth::user()->authorize('products_module_products_manage');
        
        $data['activePage'] = ['products' => 'products'];
        $data['breadcrumb'] = [
            ['title' => 'Products'],
            ['title' => 'Exporting Excel'],
        ];
        $user = \Auth::user();
        if($user->hasRole('vendor')){
            $vendorsParent = \Modules\Vendors\Entities\Vendors::with('children')->where('user_id', $user->id)->first();
            $vendor_ids = $vendorsParent->children()->pluck('id');
            $vendor_ids->push($vendorsParent->id);
            $branches =\Modules\Vendors\Entities\Vendors::with('user.province')->whereIn('id', $vendor_ids)->get();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            return view('products::export-products-for-vendor',[
                'data' => $data,
                'categories' => \Modules\Products\Entities\Category::where('vendor_type', $vendor->type_id)->get(),
                'branches' => $branches
            ]);
        }
        return view('products::export-products',[
            'data' => $data,
            'types' => \Modules\Vendors\Entities\TypeOFVendor::get(),
            'vendors' => \Modules\Vendors\Entities\Vendors::get(),

        ]);
    }
    
    public function import(Request  $request) {
        \Auth::user()->authorize('products_module_products_store');

        $user = \Auth::user();
        
        if($user->hasRole('vendor') && \Auth::user()->hasRole('main branch supplier')){
            if(!$request->branch_id){
                return response()->json(['message' => 'Not Allow'],403);
            }
            $vendor =\Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            $branch = \Modules\Vendors\Entities\Vendors::
            where('id',$request->branch_id)
            ->where('parent_id', $vendor->id)
            ->first();
            if(!$branch){
                return response()->json(['message' =>'Not Allowed'],403); 
            }    
           $request->merge([
                'vendor_id'=> $branch->id,
                'status_id' => $user->hasRole('trust_vendor') ? '1' : '2'
            ]);
        }else if($user->hasRole('vendor')){
            $user = \Auth::user();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
           $request->merge([
                'vendor_id'=> $vendor->id,
                'status_id' => $user->hasRole('trust_vendor') ? '1' : '2'
            ]);
        }
        Excel::import(new \App\Imports\ProductsImport($request), $request->file('products_file'));
        return response()->json(['message' => 'ok'], 200);

    }
    public function export(Request $request) {
        \Auth::user()->authorize('products_module_products_manage');
        $user = \Auth::user();
        if($user->hasRole('vendor') && \Auth::user()->hasRole('main branch supplier')){
            if(!$request->branch_id){
                return response()->json(['message' => 'Not Allow'],403);
            }
            $vendor =\Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            $branch = \Modules\Vendors\Entities\Vendors::
            where('id',$request->branch_id)
            ->where('parent_id', $vendor->id)
            ->first();
            if(!$branch){
                return response()->json(['message' =>'Not Allowed'],403); 
            }    
           $request->merge([
                'vendor_id'=> $branch->id,
            ]);
        }else if($user->hasRole('vendor')){
            $user = \Auth::user();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
           $request->merge(['vendor_id'=> $vendor->id]);
        }
       
        return Excel::download(new \App\Exports\ProductsExport($request), 'product.xlsx');
    }

    public function changeStatusForAdmin(Request $request, $id){
        \Auth::user()->authorize('products_module_products_change_status');
        
        $request->validate([
            'status' => 'required'
        ]);
        $product = \Modules\Products\Entities\Product::whereId($id)->first();
        $product->admin_status = $request->status;
        $product->save();
        return response()->json(['message' => 'ok'], 200);

    }

    public function addAttributesToProduct(Request $request){
        \Auth::user()->authorize('products_module_products_store');

        $request->validate([
            'product_id' => 'required',
            'price' => 'required'
        ]);

        \DB::beginTransaction();
        try {
            if($request['attributes']){
                foreach ($request['attributes'] as  $attribute) {
                    $product = \Modules\Products\Entities\Product::whereId($request->product_id)->first();
                    if(isset($attribute['variation_id'])){
                        $productVariation = \Modules\Products\Entities\ProductVariation::whereId($attribute['variation_id'])->first();
                        if(!$productVariation){
                            return response()->json(['message'=> 'Not Allow'],403);
                        }
                        // if($attribute['value1'] != null){
                        //     $variationAttribute = \Modules\Products\Entities\VariationAttribute::where('variation_id', $attribute['variation_id'])
                        //     ->where('type_id',$attribute['attibute1'])->first();
                        //     if(! $variationAttribute){
                        //         $variationAttribute = \Modules\Products\Entities\VariationAttribute::where('variation_id', $attribute['variation_id'])
                        //         ->where('type_id',$attribute['attibute1'])->first();
                        //     }
                        //     $variationAttribute->type_id = $attribute['attibute1'];
                        //     $variationAttribute->value = $attribute['value1'];
                        //     $variationAttribute->save();

                        // }
                        // if($attribute['value2'] != null){
                        //     $variationAttribute = \Modules\Products\Entities\VariationAttribute::where('variation_id', $attribute['variation_id'])
                        //     ->where('type_id',$attribute['attibute2'])->first();
                            
                        //     // return response()->json($request['attributes'],403);
                        //     $variationAttribute->type_id = $attribute['attibute2'];
                        //     $variationAttribute->value = $attribute['value2'];
                        //     $variationAttribute->save();


                        // }
                        
                        $productVariation->price = $attribute['price'];
                        $productVariation->quantity = $attribute['quantity'];
                        $productVariation->save();
                    
                    }else{
                        if($attribute['value1'] != null || $attribute['value2'] ){
                            $DefaultAttribute = \Modules\Products\Entities\VariationAttribute::with('variation')->where('type_id', '1')
                            ->whereHas('variation.product', function($q) use($request){
                                $q->where('id', $request->product_id);
                            })
                            ->first();
                            $productVariation =new \Modules\Products\Entities\ProductVariation();
                            $productVariation->product_id = $request->product_id;
                            $productVariation->created_by  = \Auth::user()->id;
                            $productVariation->save();
                            $variationAttribute =new \Modules\Products\Entities\VariationAttribute;
                            $variationAttribute->variation_id = $productVariation->id;
                            if($attribute['value1'] != null){
                                $variationAttribute =new \Modules\Products\Entities\VariationAttribute;
                                $variationAttribute->variation_id = $productVariation->id;
                                $variationAttribute->type_id = $attribute['attibute1'];
                                $variationAttribute->value = $attribute['value1'];
                                $variationAttribute->save();

                            }
                            if($attribute['value2'] != null){
                                $variationAttribute =new \Modules\Products\Entities\VariationAttribute;
                                $variationAttribute->variation_id = $productVariation->id;
                                $variationAttribute->type_id = $attribute['attibute2'];
                                $variationAttribute->value = $attribute['value2'];
                                $variationAttribute->save();

                            }
                            $productVariation->price = $attribute['price'];
                            $productVariation->quantity = $attribute['quantity'];
                            $productVariation->save();
                        }
                    }
            }
           }
            $DefaultAttribute = \Modules\Products\Entities\VariationAttribute::with('variation')->where('type_id', '1')
            ->whereHas('variation.product', function($q) use($request){
                $q->where('id', $request->product_id);
            })
            ->first();
            if(!$DefaultAttribute){
                $productVariation =new \Modules\Products\Entities\ProductVariation();
                $productVariation->product_id = $request->product_id;
                $productVariation->created_by  = \Auth::user()->id;
                $productVariation->save();
                $variationAttribute =new \Modules\Products\Entities\VariationAttribute;

            }else{
                $productVariation = \Modules\Products\Entities\ProductVariation::whereId($DefaultAttribute->variation_id)->first();
                $variationAttribute = \Modules\Products\Entities\VariationAttribute::whereId($DefaultAttribute->id)->first();

            }
            $variationAttribute->variation_id = $productVariation->id;
            $variationAttribute->type_id = '1'; 
            $productVariation->price = $request->price;
            $productVariation->quantity =$request->quantity;
            $productVariation->save();
            $variationAttribute->save();

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }
        return response()->json(['message' => 'ok']);


    }
    public function productVariationDelete($id){
        \Auth::user()->authorize('products_module_products_destroy');
        $user = \Auth::user();
        \DB::beginTransaction();
        try {
            if(auth()->user()->hasRole('super_admin')){
                $productVariation = \Modules\Products\Entities\ProductVariation::whereId($id)->first();
                $productVariation->delete();
                \DB::commit();
                return response()->json(['message' => 'ok']);

            }
            $vendor = \Modules\Vendors\Entities\Vendors::with('children')->where('user_id', $user->id)->first();
            if(!$vendor){
                return response()->json(['message' =>'Not Allowed'],403); 
            }
            $branch = null;
            if($user->hasRole('vendor') && $user->hasRole('main branch supplier')){
                
                if($vendor->children){
                    $productVariation = \Modules\Products\Entities\ProductVariation::whereId($id)->whereHas('product.vendor', function($q)use($vendor){
                        $q->where('id', $vendor->id)->orWhere('parent_id', $vendor->id);
                    })->first();
                }else{
                    $productVariation = \Modules\Products\Entities\ProductVariation::whereId($id)->whereHas('product.vendor', function($q)use($vendor){
                        $q->where('id', $vendor->id);
                    })->first();
                }
                if(!$productVariation){
                    return response()->json(['message' =>'Not Allowed'],403); 
                }
            }
            $productVariation = \Modules\Products\Entities\ProductVariation::whereId($id)->first();
            $productVariation->delete();
        \DB::commit();
    } catch (\Exception $e) {
        \DB::rollback();
        return response()->json(['message' => $e->getMessage()], 403);
    }
    return response()->json(['message' => 'ok']);
    }
}
