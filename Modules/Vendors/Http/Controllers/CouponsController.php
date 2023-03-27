<?php

namespace Modules\Vendors\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class CouponsController extends Controller{
    public function __construct(){
        $this->middleware(['auth'])->except(['index']);
    }

    public function manage(Request $request){
        \Auth::user()->authorize('vendors_module_coupons_manage');

        $data['activePage'] = ['vendors' => 'coupons'];
        $data['breadcrumb'] = [
            ['title' => 'Coupons'],
        ];
        $data['addRecord'] = ['href' => route('coupons.create')];
        if ($request->ajax()) {
            $user =  \Auth::user();
            if($user->hasRole('vendor')){
                $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
                $data = \Modules\Vendors\Entities\Coupon::with('created_by_user','vendor','type','admin_status')
                ->where('vendor_id', $vendor->id)
                ->select('*');
            }else{
                $data = \Modules\Vendors\Entities\Coupon::with('created_by_user','vendor','type','admin_status')->select('*');
            }   
            return Datatables::of($data)
            ->addColumn('action', function ($coupon) {
                $btn = '';
                $user= \Auth::user();
                if($user->hasRole('super_admin')){
                    $btn .= '<button type="button" data-action="changeState" class="btn btn-sm btn-danger "  data-toggle="modal" data-target="#changeStatus' . $coupon->id . '">';
                    $btn .= "change Status";
                    $btn .= '</button>';
                    $btn .= '<div class="modal fade"  id="changeStatus'. $coupon->id. '" tabindex="-1" role="dialog" aria-labelledby="changeStatus'. $coupon->id.'" aria-hidden="true">';
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
    
                        $btn .= '<option data-id="'. $coupon->id. '" value="';
                       $btn .= $admin_status->id;
                       $btn .= '"';
                       if($admin_status->id   == $coupon->admin_status){
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
                    $btn .= ' <div class="modal-footer"><button type="button" data-id="'. $coupon->id. '" data-action="save-new-status" class="btn btn-primary">Save changes</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                    $btn .= '</div></div></div></div>';
                }
                $btn .= '<a data-action="edit" href=' .route('coupons.edit', $coupon->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
                $btn .= '<a data-action="destroy" data-id='. $coupon->id. '  class="btn btn-xs red p-2  tooltips"><i class="fa fa-times" aria-hidden="true"></i> </a>';
                return $btn;
            })
            ->addColumn('modalEn', function ($coupon) {
                $btn = '';
                
                $btn .= '<button type="button" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#descEn' . $coupon->id . '">';
                // $btn .= '<i class="fa-solid fa-eye"></i>';
                $btn .= "Show";
                $btn .= '</button>';
                $btn .= '<div class="modal fade" id="descEn'. $coupon->id. '" tabindex="-1" role="dialog" aria-labelledby="descEn'. $coupon->id.'" aria-hidden="true">';
                $btn .= '  <div class="modal-dialog" role="document">';
                $btn .= '<div class="modal-content">';
                $btn .= '<div class="modal-header">';
                $btn .= '<h5 class="modal-title" id="">Description</h5>';
                $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                $btn .= '<div class="modal-body">';
                $btn .=$coupon->getTranslations('description')['en'];
                $btn .= '</div>';
                $btn .= ' <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                $btn .= '</div></div></div></div>';
                return $btn;
            })
            ->addColumn('modalAr', function ($coupon) {
                $btn = '';
                $btn .= '<button type="button" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#descAr' . $coupon->id . '">';
                $btn .= "Show";

                $btn .= '</button>';
                $btn .= '<div class="modal fade" id="descAr'. $coupon->id. '" tabindex="-1" role="dialog" aria-labelledby="descAr'. $coupon->id.'" aria-hidden="true">';
                $btn .= '  <div class="modal-dialog" role="document">';
                $btn .= '<div class="modal-content">';
                $btn .= '<div class="modal-header">';
                $btn .= '<h5 class="modal-title" id="">Description</h5>';
                $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                $btn .= '<div class="modal-body">';
                $btn .= $coupon->getTranslations('description')['ar'];
                $btn .= '</div>';
                $btn .= ' <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                $btn .= '</div></div></div></div>';
                return $btn;
            })
            ->addColumn('status', function ($coupon) {
                if($coupon->status == '1'){
                    return ' active';
                }else if('status' == '2'){
                    return ' rejected';

                }
            })
            ->addColumn('img', function ($coupon) {
               return  '<img src="'.$coupon->image_url.'" alt="Girl in a jacket" width="80" height="80">';
            })
            ->rawColumns(['action', 'modalEn' ,'modalAr','img','status'])
            ->filter(function ($query) use ($request) {
                if ($request->has('name')) {
                    $query->where('name', 'like', "%{$request->get('name')}%");
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

        return view('vendors::coupons',[
            'data' => $data,
            'vendors' => \Modules\Vendors\Entities\Vendors::get(),
        ]);
    }
    public function create(){
        \Auth::user()->authorize('vendors_module_coupons_manage');

        $data['activePage'] = ['vendors' => 'coupons'];
        $data['breadcrumb'] = [
            ['title' => 'Coupons'],
            ['title' => 'Add Coupon Info'],
        ];
        $user = \Auth::user();
        if($user->hasRole('vendor')){
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            return view('vendors::create-coupons-by-vendor',[
                'data' => $data,
                'types' => \Modules\Vendors\Entities\CouponsType::whereId('2')->get()
            ]); 
        }
        return view('vendors::create-coupons',[
            'data' => $data,
            'vendors' => \Modules\Vendors\Entities\Vendors::get(),
            'types' => \Modules\Vendors\Entities\CouponsType::whereId('2')->get()
        ]);
    }

    public function storeForVendor(Request $request){
        \Auth::user()->authorize('vendors_module_coupons_store');

        $request->validate([
            'code' => 'required',
            'name_ar' => 'required',
            'name_en' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'starting_data' => 'required',
            'type_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $user = \Auth::user();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            if(!$vendor){
                return response()->json(['message' =>'Not Allowed'],403); 
            }
            $offerAvilable = \Modules\Vendors\Entities\Offer::where('vendor_id', $vendor->id)->first();
            if ($offerAvilable) {
                return response()->json(['message' => 'You Can\'t Add  Coupon You Have Avilable Offers'],403); 
            }
           
            $coupon = \Modules\Vendors\Entities\Coupon::where('code', $request->code)
            ->where('vendor_id', $vendor->id)
            ->first();
            if($coupon){
                return response()->json([
                    'message' => 'This Code Is Already Exisit'
                ],403);
            }
            $coupon = new \Modules\Vendors\Entities\Coupon;
            $coupon->code = $request->code;
            $coupon
            ->setTranslation('name', 'en',  $request->name_en)
            ->setTranslation('name', 'ar',   $request->name_ar);
            $coupon
            ->setTranslation('description', 'en',  $request->description_en)
            ->setTranslation('description', 'ar',   $request->description_ar);
            $coupon->vendor_id = $vendor->id;
            $coupon->amount = $request->amount;
            $coupon->starting_data = $request->starting_data;
            $coupon->ended_data = $request->ended_data;
            $coupon->type_id  = $request->type_id ;
            $coupon->status = '1';
            $user->hasRole('trust_vendor') ? $coupon->admin_status  = '1' : $coupon->admin_status  = '2';
            $coupon->created_by  = \Auth::user()->id;

            $coupon->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
    }
    public function store(Request $request){
        \Auth::user()->authorize('vendors_module_coupons_store');

        $request->validate([
            'code' => 'required',
            'name_ar' => 'required',
            'name_en' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'starting_data' => 'required',
            'type_id' => 'required',
            'status' => 'required',
            'vendor_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $offerAvilable = \Modules\Vendors\Entities\Offer::where('vendor_id', $request->vendor_id)->first();
            if ($offerAvilable) {
                return response()->json(['message' => 'You Can\'t Add  Coupon You Have Avilable Offers'],403); 
            }
           
            $coupon = \Modules\Vendors\Entities\Coupon::where('code', $request->code)
            ->where('vendor_id', $request->vendor_id)
            ->first();
            if($coupon){
                return response()->json([
                    'message' => 'This Code Is Already Exisit'
                ],403);
            }
            $coupon = new \Modules\Vendors\Entities\Coupon;
            $coupon->code = $request->code;
            $coupon
            ->setTranslation('name', 'en',  $request->name_en)
            ->setTranslation('name', 'ar',   $request->name_ar);
            $coupon
            ->setTranslation('description', 'en',  $request->description_en)
            ->setTranslation('description', 'ar',   $request->description_ar);
            $coupon->vendor_id = $request->vendor_id;
            $coupon->amount = $request->amount;
            $coupon->starting_data = $request->starting_data;
            $coupon->ended_data = $request->ended_data;
            $coupon->type_id  = $request->type_id ;
            $coupon->status = $request->status;
            $coupon->created_by  = \Auth::user()->id;

            $coupon->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
    }

    public function edit($id){
        \Auth::user()->authorize('vendors_module_coupons_manage');

        $coupon = \Modules\Vendors\Entities\Coupon::whereId($id)->first();
        $data['activePage'] = ['vendors' => 'coupons'];
        $data['breadcrumb'] = [
            ['title' => 'Vendor Types'],
            ['title' => 'Edit Vendor Type'],
        ];
        return view('vendors::edit-coupons',[
            'data' => $data,
            'coupon' => $coupon,
            'vendors' => \Modules\Vendors\Entities\Vendors::get(),
            'types' => \Modules\Vendors\Entities\CouponsType::whereId('2')->get()
        ]);
    }

    public function updateForVendor(Request $request, $id){
        \Auth::user()->authorize('vendors_module_coupons_update');

        $request->validate([
            'code' => 'required',
            'name_ar' => 'required',
            'name_en' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'starting_data' => 'required',
            'type_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $user = \Auth::user();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            if(!$vendor){
                return response()->json(['message' =>'Not Allowed'],403); 
            }
            $offerAvilable = \Modules\Vendors\Entities\Offer::where('vendor_id',$vendor->id)->first();
            if ($offerAvilable) {
                return response()->json(['message' => 'You Can\'t Add  Coupon You Have Avilable Offers'],403); 
            }
           
            $coupon = \Modules\Vendors\Entities\Coupon::where('id', '<>', $id)
            ->where('vendor_id',$vendor->id)
            ->where('code', trim($request->code))->first();
            if($coupon){
                return response()->json([
                    'message' => 'This Code Is Already Exisit'
                ],403);
            }
            $coupon = \Modules\Vendors\Entities\Coupon::whereId($id)->first();
            $coupon->code = $request->code;
            $coupon
            ->setTranslation('name', 'en',  $request->name_en)
            ->setTranslation('name', 'ar',   $request->name_ar);
            $coupon
            ->setTranslation('description', 'en',  $request->description_en)
            ->setTranslation('description', 'ar',   $request->description_ar);
            $coupon->vendor_id =$vendor->id;
            $coupon->amount = $request->amount;
            $coupon->value = $request->value;
            $coupon->starting_data = $request->starting_data;
            $coupon->ended_data = $request->ended_data;
            $coupon->type_id  = $request->type_id ;
            $coupon->status = '2';
            $coupon->created_by  = \Auth::user()->id;

            $coupon->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
    }
    public function update(Request $request, $id){
        \Auth::user()->authorize('vendors_module_coupons_update');

        $request->validate([
            'code' => 'required',
            'name_ar' => 'required',
            'name_en' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'starting_data' => 'required',
            'type_id' => 'required',
            'status' => 'required',
            'vendor_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $offerAvilable = \Modules\Vendors\Entities\Offer::where('vendor_id', $request->vendor_id)->first();
            if ($offerAvilable) {
                return response()->json(['message' => 'You Can\'t Add  Coupon You Have Avilable Offers'],403); 
            }
           
            $coupon = \Modules\Vendors\Entities\Coupon::where('id', '<>', $id)
            ->where('vendor_id', $request->vendor_id)
            ->where('code', trim($request->code))->first();
            if($coupon){
                return response()->json([
                    'message' => 'This Code Is Already Exisit'
                ],403);
            }
            $coupon = \Modules\Vendors\Entities\Coupon::whereId($id)->first();
            $coupon->code = $request->code;
            $coupon
            ->setTranslation('name', 'en',  $request->name_en)
            ->setTranslation('name', 'ar',   $request->name_ar);
            $coupon
            ->setTranslation('description', 'en',  $request->description_en)
            ->setTranslation('description', 'ar',   $request->description_ar);
            $coupon->vendor_id = $request->vendor_id;
            $coupon->amount = $request->amount;
            $coupon->value = $request->value;
            $coupon->starting_data = $request->starting_data;
            $coupon->ended_data = $request->ended_data;
            $coupon->type_id  = $request->type_id ;
            $coupon->status = $request->status;
            $coupon->created_by  = \Auth::user()->id;

            $coupon->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
    }

    public function destroy(Request $request, $id){
        \Auth::user()->authorize('vendors_module_coupons_destroy');

        \Modules\Vendors\Entities\Coupon::destroy($id);
        return response()->json('Ok',200);
    }
    public function changeStatusForAdmin(Request $request, $id){
        \Auth::user()->authorize('core_module_coupons_change_status');
        
        $request->validate([
            'status' => 'required'
        ]);
        $product = \Modules\Vendors\Entities\Coupon::whereId($id)->first();
        $product->admin_status = $request->status;
        $product->save();
        return response()->json(['message' => 'ok'], 200);

    }
}
