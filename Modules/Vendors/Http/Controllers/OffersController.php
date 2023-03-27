<?php

namespace Modules\Vendors\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class OffersController extends Controller{
    public function __construct(){
        $this->middleware(['auth'])->except(['index']);
    }
    public function manage(Request $request){
        \Auth::user()->authorize('vendors_module_offer_manage');

        $data['activePage'] = ['vendors' => 'offers'];
        $data['breadcrumb'] = [
            ['title' => 'Offer'],
        ];
        
        $data['addRecord'] = ['href' => route('offers.create')];
        if ($request->ajax()) {
            $user =  \Auth::user();
            if($user->hasRole('vendor')){
                $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
                $data = \Modules\Vendors\Entities\Offer::with('created_by_user','vendor','type','admin_status')
                ->where('vendor_id', $vendor->id)
                ->select('*');
            }else{
                $data = \Modules\Vendors\Entities\Offer::with('created_by_user','vendor','type','admin_status')->select('*');
            }
            return Datatables::of($data)
            ->addColumn('action', function ($offer) {
                $btn = '';
                $user= \Auth::user();
                if($user->hasRole('super_admin')){
                    $btn .= '<button type="button" data-action="changeState" class="btn btn-sm btn-danger "  data-toggle="modal" data-target="#changeStatus' . $offer->id . '">';
                    $btn .= "change Status";
                    $btn .= '</button>';
                    $btn .= '<div class="modal fade"  id="changeStatus'. $offer->id. '" tabindex="-1" role="dialog" aria-labelledby="changeStatus'. $offer->id.'" aria-hidden="true">';
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
    
                        $btn .= '<option data-id="'. $offer->id. '" value="';
                       $btn .= $admin_status->id;
                       $btn .= '"';
                       if($admin_status->id   == $offer->admin_status){
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
                    $btn .= ' <div class="modal-footer"><button type="button" data-id="'. $offer->id. '" data-action="save-new-status" class="btn btn-primary">Save changes</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                    $btn .= '</div></div></div></div>';
                }
                $btn .= '<a data-action="edit" href=' .route('offers.edit', $offer->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
                $btn .= '<a data-action="destroy" data-id='. $offer->id. '  class="btn btn-xs red p-2  tooltips"><i class="fa fa-times" aria-hidden="true"></i> </a>';
                return $btn;
            })
            ->addColumn('modalEn', function ($offer) {
                $btn = '';
                $btn .= '<button type="button" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#descEn' . $offer->id . '">';
                // $btn .= '<i class="fa-solid fa-eye"></i>';
                $btn .= "Show";
                $btn .= '</button>';
                $btn .= '<div class="modal fade" id="descEn'. $offer->id. '" tabindex="-1" role="dialog" aria-labelledby="descEn'. $offer->id.'" aria-hidden="true">';
                $btn .= '  <div class="modal-dialog" role="document">';
                $btn .= '<div class="modal-content">';
                $btn .= '<div class="modal-header">';
                $btn .= '<h5 class="modal-title" id="">Description</h5>';
                $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                $btn .= '<div class="modal-body">';
                $btn .=$offer->getTranslations('description')['en'];
                $btn .= '</div>';
                $btn .= ' <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                $btn .= '</div></div></div></div>';
                return $btn;
            })
            ->addColumn('modalAr', function ($offer) {
                $btn = '';
                $btn .= '<button type="button" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#descAr' . $offer->id . '">';
                $btn .= "Show";

                $btn .= '</button>';
                $btn .= '<div class="modal fade" id="descAr'. $offer->id. '" tabindex="-1" role="dialog" aria-labelledby="descAr'. $offer->id.'" aria-hidden="true">';
                $btn .= '  <div class="modal-dialog" role="document">';
                $btn .= '<div class="modal-content">';
                $btn .= '<div class="modal-header">';
                $btn .= '<h5 class="modal-title" id="">Description</h5>';
                $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                $btn .= '<div class="modal-body">';
                $btn .= $offer->getTranslations('description')['ar'];
                $btn .= '</div>';
                $btn .= ' <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                $btn .= '</div></div></div></div>';
                return $btn;
            })
            ->addColumn('status', function ($ad) {
                if($ad->status == '1'){
                    return 'active';
                }else if($ad->status == '2'){
                    return 'rejected';

                }
                
            })
            ->addColumn('img', function ($ad) {
               return  '<img src="'.$ad->image_url.'" alt="Girl in a jacket" width="80" height="80">';
            })
            ->rawColumns(['action', 'modalEn' ,'modalAr','img','status'])
            ->filter(function ($query) use ($request) {
                if ($request->has('name')) {
                    $query->where('name', 'like', "%{$request->get('name')}%");
                }
    
                if ($request->has('description')) {
                    $query->where('description', 'like', "%{$request->get('description')}%");
                }
                if ($request->has('vendor_id') && $request->get('vendor_id') != null) {
                    $query->where('vendor_id', trim($request->get('vendor_id')));
                }
                if ($request->has('status') && $request->get('status') != null) {
                    $query->where('status', trim($request->get('status')));
                }
                if ($request->has('type_id') && $request->get('type_id') != null) {
                    $query->where('type_id', trim($request->get('type_id')));
                }
                
            })
            ->toJson();
        }

        return view('vendors::offer',[
            'data' => $data,
            'vendors' => \Modules\Vendors\Entities\Vendors::get(),
        ]);
    }
    public function create(){
        \Auth::user()->authorize('vendors_module_offer_manage');

        $data['activePage'] = ['vendors' => 'offers'];
        $data['breadcrumb'] = [
            ['title' => 'Offer'],
            ['title' => 'Add Offer Info'],
        ];
        $user = \Auth::user();
        if($user->hasRole('vendor')){
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            return view('vendors::create-offers-by-vendor',[
                'data' => $data,
                'types' => \Modules\Vendors\Entities\CouponsType::where('id','<>','2')->get(),
                'products' => \Modules\Products\Entities\Product::where('vendor_id', $vendor->id)->get()
            ]); 
        }
        return view('vendors::create-offers',[
            'data' => $data,
            'vendors' => \Modules\Vendors\Entities\Vendors::get(),
            'types' => \Modules\Vendors\Entities\CouponsType::where('id','<>','2')->get()

        ]);
    }
    public function edit($id){
        \Auth::user()->authorize('vendors_module_offer_manage');

        $data['activePage'] = ['vendors' => 'offers'];
        $data['breadcrumb'] = [
            ['title' => 'Offer'],
            ['title' => 'Edit Offer Info'],
        ];
        $user = \Auth::user();
        if($user->hasRole('vendor')){
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
           $offer = \Modules\Vendors\Entities\Offer::whereId($id)->first();
            return view('vendors::edit-offer-by-vendor',[
                'data' => $data,
                'types' => \Modules\Vendors\Entities\CouponsType::where('id','<>','2')->get(),
                'products' => \Modules\Products\Entities\Product::where('vendor_id', $vendor->id)->get(),
                'products_list' => \Modules\Vendors\Entities\OfferProduct::where('offer_id', $id)->pluck('product_id')->toArray(),
                'offer' => $offer,
            ]); 
        }
        $offer = \Modules\Vendors\Entities\Offer::whereId($id)->first();
        return view('vendors::edit-offer',[
            'data' => $data,
            'offer' => $offer,
            'vendors' => \Modules\Vendors\Entities\Vendors::get(),
            'types' => \Modules\Vendors\Entities\CouponsType::where('id','<>','2')->get(),
            'products_list' => \Modules\Vendors\Entities\OfferProduct::where('offer_id', $id)->pluck('product_id')->toArray(),
            'products' => \Modules\Products\Entities\Product::where('vendor_id',$offer->vendor_id)->get()
        ]);
    }
    public function storeForVendor(Request $request){
        \Auth::user()->authorize('vendors_module_offer_store');
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'starting_data' => 'required',
            'ended_data' => 'required',
            'type_id' => 'required',
            'products_id' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $user = \Auth::user();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            if(!$vendor){
                return response()->json(['message' =>'Not Allowed'],403); 
            }
            $coupon = \Modules\Vendors\Entities\Coupon::where('vendor_id', $vendor->id)->first();
            if($coupon){
                return response()->json(['message' => 'You can\'t Add Offer You have Avilable Coupons'],403);
            }
            $request->type_id == '3' && !count($request->products_id) > 0  ? $request->merge(['products_id' => \Modules\Products\Entities\Product::where('vendor_id', $request->vendor_id)->get()])  : '';

            $offerAvilable = \Modules\Vendors\Entities\OfferProduct::whereHas('offer', function($q)use($request, $vendor){
                $q->where('vn_offers.vendor_id', $vendor->id);
                $q->whereIn('vn_offers_products.product_id', $request->products_id);
            })
            ->get();
            if(count($offerAvilable) > 0){
                return response()->json(['message' => 'You Can\'t Add  Product Exsist In Another Order'],403); 
            }
            if($request->type_id == '1' && $request->value == null){
                return response()->json(['message' => 'That Should Add Percentage of discount']);
            }
            if($request->type_id == '3' && $request->amount == null){
                return response()->json(['message' => 'That Should Add Amount To Free Shipping Offer']);
            }
            $offer = new \Modules\Vendors\Entities\Offer;
            $offer
            ->setTranslation('name', 'en',  $request->name_en)
            ->setTranslation('name', 'ar',   $request->name_ar);
            $offer
            ->setTranslation('description', 'en',  $request->description_en)
            ->setTranslation('description', 'ar',   $request->description_ar);
            $offer->starting_data = $request->starting_data;
            $offer->ended_data = $request->ended_data;
            $offer->vendor_id = $vendor->id;
            $offer->status = '1';
            $user->hasRole('trust_vendor') ? $offer->admin_status  = '1' : $offer->admin_status  = '2';
            $offer->amount = $request->amount;
            $offer->value = $request->value;
            $offer->type_id = $request->type_id;
            $offer->created_by  = \Auth::user()->id;

            $offer->save();
            foreach($request->products_id as $product_id){
                $offer_products = new \Modules\Vendors\Entities\OfferProduct;
                $offer_products->product_id = $product_id;
                $offer_products->offer_id = $offer->id;
                $offer_products->save();
            }
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok','data' => $offer_products]);
    }
    public function store(Request $request){
        \Auth::user()->authorize('vendors_module_offer_store');
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'starting_data' => 'required',
            'ended_data' => 'required',
            'vendor_id' => 'required',
            'type_id' => 'required',
            'products_id' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $coupon = \Modules\Vendors\Entities\Coupon::where('vendor_id', $request->vendor_id)->first();
            if($coupon){
                return response()->json(['message' => 'You can\'t Add Offer You have Avilable Coupons'],403);
            }
            $request->type_id == '3' && !count($request->products_id) > 0  ? $request->merge(['products_id' => \Modules\Products\Entities\Product::where('vendor_id', $request->vendor_id)->get()])  : '';
            $offerAvilable = \Modules\Vendors\Entities\OfferProduct::whereHas('offer', function($q)use($request){
                $q->where('vn_offers.vendor_id', $request->vendor_id);
                $q->whereIn('vn_offers_products.product_id', $request->products_id);
            })
            ->get();
            if(count($offerAvilable) > 0){
                return response()->json(['message' => 'You Can\'t Add  Product Exsist In Another Order'],403); 
            }
            if($request->type_id == '3' && $request->amount == null){
                return response()->json(['message' => 'That Should Add Amount To Free Shipping Offer']);
            }
            $offer = new \Modules\Vendors\Entities\Offer;
            $offer
            ->setTranslation('name', 'en',  $request->name_en)
            ->setTranslation('name', 'ar',   $request->name_ar);
            $offer
            ->setTranslation('description', 'en',  $request->description_en)
            ->setTranslation('description', 'ar',   $request->description_ar);
            $offer->starting_data = $request->starting_data;
            $offer->ended_data = $request->ended_data;
            $offer->vendor_id = $request->vendor_id;
            $offer->status = $request->status;
            $offer->amount = $request->amount;
            $offer->value = $request->value;
            $offer->type_id = $request->type_id;
            $offer->created_by  = \Auth::user()->id;

            $offer->save();
            $request->type_id  == 3 ? '':'';
            foreach($request->products_id as $product_id){
                $offer_products = new \Modules\Vendors\Entities\OfferProduct;
                $offer_products->product_id = $product_id;
                $offer_products->offer_id = $offer->id;
                $offer_products->save();
            }
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok','data' => $offer_products]);
    }
    public function updateForVendor(Request $request, $id){
        \Auth::user()->authorize('vendors_module_offer_update');
        $request->validate([
            'products_id' => 'required',
            'name_en' => 'required',
            'name_ar' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'starting_data' => 'required',
            'ended_data' => 'required',
            'type_id' => 'required',
            'products_id' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $user = \Auth::user();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            if(!$vendor){
                return response()->json(['message' =>'Not Allowed'],403); 
            }
            $coupon = \Modules\Vendors\Entities\Coupon::where('vendor_id', $vendor->id)->first();
            if($coupon){
                return response()->json(['message' => 'You can\'t Add Offer You have Avilable Coupons'],403);
            }
            $productsArrayCount =count($request->products_id);
            if($request->type_id == '3' && $productsArrayCount == 0 &&$request->amount == null){
                return response()->json(['message' => 'That Should Add Amount To Free Shipping Offer'],403);
            }
            $offer =  \Modules\Vendors\Entities\Offer::whereId($id)->first();
            $offer
            ->setTranslation('name', 'en',  $request->name_en)
            ->setTranslation('name', 'ar',   $request->name_ar);
            $offer
            ->setTranslation('description', 'en',  $request->description_en)
            ->setTranslation('description', 'ar',   $request->description_ar);
            $offer->starting_data = $request->starting_data;
            $offer->ended_data = $request->ended_data;
            $offer->vendor_id =  $vendor->id;
            $offer->status = '2';
            $productsArrayCount == 0 ? $offer->amount = $request->amount : null;
            $offer->value = $request->value;
            $offer->type_id = $request->type_id;
            $offer->created_by  = \Auth::user()->id;

            $offer->save();
            $request->type_id == '3' && !count($request->products_id) > 0  ? $request->merge(['products_id' => \Modules\Products\Entities\Product::where('vendor_id', $request->vendor_id)->get()])  : '';

            $offer_products =  \Modules\Vendors\Entities\OfferProduct::where('offer_id', $id)->delete();
            $offerAvilable = \Modules\Vendors\Entities\OfferProduct::whereHas('offer', function($q)use($request,$offer, $vendor){
                $q->where('vn_offers.vendor_id',  $vendor->id);
                $q->whereIn('vn_offers_products.product_id', $request->products_id);
                $q->where('id', '<>', $offer->id);
            })
            ->get();
            if(count($offerAvilable) > 0){
                return response()->json(['message' => 'You Can\'t Add  Product Exsist In Another Order'],403); 
            }
            foreach($request->products_id as $product_id){
                $offer_products = new \Modules\Vendors\Entities\OfferProduct;
                $offer_products->product_id = $product_id;
                $offer_products->offer_id = $offer->id;
                $offer_products->save();
            }
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok','data' => $offer_products]);
    }
    public function update(Request $request, $id){
        \Auth::user()->authorize('vendors_module_offer_update');
        $request->validate([
            // 'products_id' => 'required',
            'name_en' => 'required',
            'name_ar' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'starting_data' => 'required',
            'ended_data' => 'required',
            'vendor_id' => 'required',
            'type_id' => 'required',
            // 'products_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            // $coupon = \Modules\Vendors\Entities\Coupon::where('vendor_id', $request->vendor_id)->first();
            // if($coupon){
            //     return response()->json(['message' => 'You can\'t Add Offer You have Avilable Coupons'],403);
            // }
            !isset($request->products_id) ? $request->products_id = [] :'';
            $productsArrayCount = count($request->products_id);
            if($request->type_id == '3' && $productsArrayCount == 0 &&$request->amount == null){
                return response()->json(['message' => 'That Should Add Amount To Free Shipping Offer'],403);
            }
            if($request->type_id == '1' && $request->value == null){
                return response()->json(['message' => 'That Should Add Percentage of discount']);
            }
            $offer =  \Modules\Vendors\Entities\Offer::whereId($id)->first();
            $offer
            ->setTranslation('name', 'en',  $request->name_en)
            ->setTranslation('name', 'ar',   $request->name_ar);
            $offer
            ->setTranslation('description', 'en',  $request->description_en)
            ->setTranslation('description', 'ar',   $request->description_ar);
            $offer->starting_data = $request->starting_data;
            $offer->ended_data = $request->ended_data;
            $offer->vendor_id = $request->vendor_id;
            $offer->status = $request->status;
            $productsArrayCount == 0 ? $offer->amount = $request->amount : null;
            $offer->value = $request->value;
            $offer->type_id = $request->type_id;
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $extension = strtolower($request->file('image')->extension());
                $media_new_name = strtolower(md5(time())) . "." . $extension;
                $collection = "offer-image";
    
                $offer->addMediaFromRequest('image')
                    ->usingFileName($media_new_name)
                    ->usingName($request->file('image')->getClientOriginalName())
                    ->toMediaCollection($collection);
            }
            $offer->created_by  = \Auth::user()->id;

            $offer->save();
            $request->type_id == '3' && !count($request->products_id)  ? $request->merge(['products_id' => \Modules\Products\Entities\Product::where('vendor_id', $request->vendor_id)->pluck('id')])  : '';
            $offer_products =  \Modules\Vendors\Entities\OfferProduct::where('offer_id', $id)->delete();
            $offerAvilable = \Modules\Vendors\Entities\OfferProduct::whereHas('offer', function($q)use($request,$offer){
                $q->where('vn_offers.vendor_id', $request->vendor_id);
                $q->whereIn('vn_offers_products.product_id', $request['products_id']);
                $q->where('id', '<>', $offer->id);
            })
            ->get();
            if(count($offerAvilable) > 0){
                return response()->json(['message' => 'You Can\'t Add  Product Exsist In Another Order'],403); 
            }
            foreach($request['products_id'] as $product_id){
                $offer_products = new \Modules\Vendors\Entities\OfferProduct;
                $offer_products->product_id = $product_id;
                $offer_products->offer_id = $offer->id;
                $offer_products->save();
            }
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok','data' => $offer_products]);
    }

    public function destroy(Request $request, $id){
        \Auth::user()->authorize('vendors_module_offer_destroy');

        \Modules\Vendors\Entities\Offer::destroy($id);
        return response()->json('Ok',200);
    }

    public function addImage(Request $request){
        $id = $request->userId;
        $ad = \Modules\Vendors\Entities\Offer::whereId($id)->first();
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $extension = strtolower($request->file('file')->extension());
            $media_new_name = strtolower(md5(time())) . "." . $extension;
            $collection = "offer-image";

            $ad->addMediaFromRequest('file')
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
    
    public function show($id){
        $ad = \Modules\Vendors\Entities\Offer::whereId($id)->first();
        $images = $ad->getMedia('offer-image');
        $images_new  = collect([]);
        foreach($images as $image){
            $new['url'] =  url('/') . '/storage/app/public/' . $image->id . '/' . $image->file_name;
            $new['name'] = $image->file_name;
            $images_new->push($new);
        }
        return response()->json($images_new,200);
    }
    public function changeStatusForAdmin(Request $request, $id){
        \Auth::user()->authorize('core_module_offer_change_status');
        
        $request->validate([
            'status' => 'required'
        ]);
        $product = \Modules\Vendors\Entities\Offer::whereId($id)->first();
        $product->admin_status = $request->status;
        $product->save();
        return response()->json(['message' => 'ok'], 200);

    }
}
