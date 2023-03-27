<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class AdsController extends Controller{
    public function __construct(){
        $this->middleware(['auth'])->except(['index','storeApi']);
    }
    public function index(){
        $data = \Modules\Core\Entities\Ad::with('created_by_user','vendor')->get();
        return response()->json($data);
    }
    public function manage(Request $request){
        \Auth::user()->authorize('core_module_ads_manage');

        $data['activePage'] = ['core' => 'ads'];
        $data['breadcrumb'] = [
            ['title' => 'Ads'],
        ];
        $data['addRecord'] = ['href' => route('ads.create')];
        if ($request->ajax()) {
            $user =  \Auth::user();
            if($user->hasRole('vendor')){
                $vendorsParent = \Modules\Vendors\Entities\Vendors::with('children')->where('user_id', $user->id)->first();
                $vendor_ids = $vendorsParent->children()->pluck('id');
                $vendor_ids->push($vendorsParent->id);
                // $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
                $data = \Modules\Core\Entities\Ad::with('created_by_user','vendor','admin_status')
                ->whereIn('vendor_id', $vendor_ids)
                ->select('*');
            }else{

                $data = \Modules\Core\Entities\Ad::with('created_by_user','vendor','admin_status')->select('*');
            }
            return Datatables::of($data)
            ->addColumn('action', function ($ad) {
                $btn = '';
                $user= \Auth::user();
                if($user->hasRole('super_admin')){
                    $btn .= '<button type="button" data-action="changeState" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#changeStatus' . $ad->id . '">';
                    $btn .= "change Status";
                    $btn .= '</button>';
                    $btn .= '<div class="modal fade"  id="changeStatus'. $ad->id. '" tabindex="-1" role="dialog" aria-labelledby="changeStatus'. $ad->id.'" aria-hidden="true">';
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
    
                        $btn .= '<option data-id="'. $ad->id. '" value="';
                       $btn .= $admin_status->id;
                       $btn .= '"';
                       if($admin_status->id   == $ad->admin_status){
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
                    $btn .= ' <div class="modal-footer"><button type="button" data-id="'. $ad->id. '" data-action="save-new-status" class="btn btn-primary">Save changes</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                    $btn .= '</div></div></div></div>';
                }
                $btn .= '<a data-action="edit" href=' .route('ads.edit', $ad->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
                $btn .= '<a data-action="destroy" data-id='. $ad->id. '  class="btn btn-xs red p-2  tooltips"><i class="fa fa-times" aria-hidden="true"></i> </a>';
                return $btn;
            })
            ->addColumn('modal', function ($ad) {
                $btn = '';
                $btn .= '<button type="button" class=""  data-toggle="modal" data-target="#descAr' . $ad->id . '">';
                $btn .= "Show";

                $btn .= '</button>';
                $btn .= '<div class="modal fade" id="descAr'. $ad->id. '" tabindex="-1" role="dialog" aria-labelledby="descAr'. $ad->id.'" aria-hidden="true">';
                $btn .= '  <div class="modal-dialog" role="document">';
                $btn .= '<div class="modal-content">';
                $btn .= '<div class="modal-header">';
                $btn .= '<h5 class="modal-title" id="">Description</h5>';
                $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                $btn .= '<div class="modal-body">';
                $btn .= $ad->description;
                $btn .= '</div>';
                $btn .= ' <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                $btn .= '</div></div></div></div>';
                return $btn;
            })
            ->addColumn('status', function ($ad) {
                if($ad->status == '1'){
                    return 'active';
                }else if('status' == '2'){
                    return 'pending';

                }else if('status' == '3'){
                    return 'rejected';

                }
                
            })
            ->addColumn('img', function ($ad) {
               return  '<img src="'.$ad->image_url.'" alt="Girl in a jacket" width="80" height="80">';
            })
            ->rawColumns(['action','modal','img','status'])
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
            })
            ->toJson();
        }

        return view('core::ads',[
            'data' => $data,
        ]);
    }
    public function create(){
        \Auth::user()->authorize('core_module_ads_manage');

        $data['activePage'] = ['core' => 'ads'];
        $data['breadcrumb'] = [
            ['title' => 'Ads'],
            ['title' => 'Add Ad Info'],
        ];
        $user = \Auth::user();
        if($user->hasRole('vendor')){
            $user = \Auth::user();
            return view('core::create-ad-by-vendor',[
                'data' => $data,
            ]);
        }
        return view('core::create-ad',[
            'data' => $data,
            'vendors' => \Modules\Vendors\Entities\Vendors::get(),
        ]);
    }
    public function storeForVendor(Request $request){
        \Auth::user()->authorize('core_module_ads_store');
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'stating_date' => 'required',
            'ended_date' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $user = \Auth::user();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            if(!$vendor){
                return response()->json(['message' =>'Not Allowed'],403); 
            }
            $ad = new \Modules\Core\Entities\Ad;
            $ad->name = $request->name;
            $ad->description = $request->description;
            $ad->stating_date = $request->stating_date;
            $ad->ended_date = $request->ended_date;
            $ad->vendor_id = $vendor->id;
            $ad->created_by = $user->id;
            $ad->status = '1';
            $user->hasRole('trust_vendor') ? $ad->admin_status  = '1' : $ad->admin_status  = '2';

            $ad->save();
            
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok', 'data' => $ad]);

    }
    public function store(Request $request){
        \Auth::user()->authorize('core_module_ads_store');
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'stating_date' => 'required',
            'ended_date' => 'required',
            'vendor_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $user =\Auth::user();
            $ad = new \Modules\Core\Entities\Ad;
            $ad->name = $request->name;
            $ad->description = $request->description;
            $ad->stating_date = $request->stating_date;
            $ad->ended_date = $request->ended_date;
            $ad->vendor_id = $request->vendor_id;
            $ad->created_by = $user->id;
            $ad->status = $request->status;
            

            $ad->save();
            
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok', 'data' => $ad]);
    }
    public function edit($id){
        \Auth::user()->authorize('core_module_ads_manage');

        $ad = \Modules\Core\Entities\Ad::with('created_by_user', 'vendor')->whereId($id)->first();
        $data['activePage'] = ['core' => 'ads'];
        $data['breadcrumb'] = [
            ['title' => 'Ads'],
            ['title' => 'Edit Ad Info'],
        ];
        $user = \Auth::user();
        if($user->hasRole('vendor')){
            $user = \Auth::user();
            return view('core::edit-ad-by-vendor',[
                'data' => $data,
                'ad' => $ad,
            ]);
        }
        return view('core::edit-ad',[
            'data' => $data,
            'ad' => $ad,
            'vendors' => \Modules\Vendors\Entities\Vendors::get(),
        ]);
    }
    public function updateForVendor(Request $request, $id){
        \Auth::user()->authorize('core_module_ads_update');
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'stating_date' => 'required',
            'ended_date' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $user = \Auth::user();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            if(!$vendor){
                return response()->json(['message' =>'Not Allowed'],403); 
            }
            $ad =  \Modules\Core\Entities\Ad::whereId($id)->first();
            $ad->name = $request->name;
            $ad->description = $request->description;
            $ad->stating_date = $request->stating_date;
            $ad->ended_date = $request->ended_date;
            $ad->status = '2';
            $ad->vendor_id = $vendor->id;
            $ad->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok','data' => $ad]);
    }
    public function update(Request $request, $id){
        \Auth::user()->authorize('core_module_ads_update');

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'stating_date' => 'required',
            'ended_date' => 'required',
            'vendor_id' => 'required',
            "status" => 'required'
        ]);

        \DB::beginTransaction();
        try {
            $user = \Auth::user();
            $ad =  \Modules\Core\Entities\Ad::whereId($id)->first();
            $ad->name = $request->name;
            $ad->description = $request->description;
            $ad->stating_date = $request->stating_date;
            $ad->ended_date = $request->ended_date;
            $ad->vendor_id = $request->vendor_id;
            $ad->status = $request->status;
            $ad->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok','data' => $ad]);
    }

    public function destroy(Request $request, $id){
        \Auth::user()->authorize('core_module_ads_destroy');

        \Modules\Core\Entities\Ad::destroy($id);
        return response()->json('Ok',200);
    }

    public function addImage(Request $request){
        $id = $request->userId;
        
        $ad = \Modules\Core\Entities\Ad::whereId($id)->first();
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $extension = strtolower($request->file('file')->extension());
            $media_new_name = strtolower(md5(time())) . "." . $extension;
            $collection = "ad-image";

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
        $ad = \Modules\Core\Entities\Ad::whereId($id)->first();
        $images = $ad->getMedia('ad-image');
        $images_new  = collect([]);
        foreach($images as $image){
            $new['url'] =  url('/') . '/storage/app/public/' . $image->id . '/' . $image->file_name;
            $new['name'] = $image->file_name;
            $images_new->push($new);
        }
        return response()->json($images_new,200);
    }
    public function changeStatusForAdmin(Request $request, $id){
        \Auth::user()->authorize('core_module_ads_change_status');
        
        $request->validate([
            'status' => 'required'
        ]);
        $product = \Modules\Core\Entities\Ad::whereId($id)->first();
        $product->admin_status = $request->status;
        $product->save();
        return response()->json(['message' => 'ok'], 200);

    }
}
