<?php

namespace Modules\Vendors\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Yajra\Datatables\Datatables;

class VendorsController extends Controller{
    public function __construct(){
        $this->middleware(['auth']);
    }  
    public function index(){
        $data = \Modules\Vendors\Entities\Vendors::get();
        return response()->json($data);
    } 
    
    public function manage(Request $request){
        \Auth::user()->authorize('vendors_module_vendors_manage');

        $data['activePage'] = ['vendors' => 'vendors'];
        $data['breadcrumb'] = [
            ['title' => 'Vendors'],
        ];
        // $data['addRecord'] = ['href' => route('vendors.create')];
        if ($request->ajax()) {
            $user =\Auth::user();
            if($user->hasRole('vendor') && $user->hasRole('main branch supplier')){
                $data = \Modules\Vendors\Entities\Vendors::with('created_by_user', 'user.province','type_of_vendor','status')
                ->where('parent_id', $user->id)
                ->orWhere('id', $user->id)
                ->select('*');
            }else{
                $data = \Modules\Vendors\Entities\Vendors::with('created_by_user', 'user.province','type_of_vendor','status')->select('*');
            }
            return Datatables::of($data)
            ->addColumn('action', function ($vendors_type) {
                $btn = '';
                $btn .= '<a data-action="edit" href=' .route('vendors.edit', $vendors_type->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
                $btn .= '<a data-action="destroy" data-id='. $vendors_type->id. '  class="btn btn-xs red p-2  tooltips"><i class="fa fa-times" aria-hidden="true"></i> </a>';
                return $btn;
            })
            ->editColumn('status_id',function($row){
                $btn = view('core.status_label')->with(['row'=>$row])->render();
                return $btn;
            })
            ->rawColumns(['action','status_id'])
            ->filter(function ($query) use ($request) {
                if ($request->has('company_name') && $request->get('company_name') != null) {
                    $query->where('company_name', 'like', "%{$request->get('company_name')}%");
                }
                if ($request->has('type_id') && $request->get('type_id') != null) {
                    $query->whereHas('type_of_vendor', function($eloquent) use ($request){
                        $eloquent->whereId(trim($request->get('type_id')));
                    });
                }
                if ($request->has('province_id') && $request->get('province_id') != null) {
                    $query->whereHas('user.province', function($eloquent) use ($request){
                        $eloquent->whereId(trim($request->get('province_id')));
                    });
                }
            })
            ->toJson();
        }

        return view('vendors::vendors',[
            'data' => $data,
            'provinces' => \Modules\Core\Entities\CountryProvince::get(),
            'types' => \Modules\Vendors\Entities\TypeOFVendor::get(),

        ]);
    }
    public function create(){
        \Auth::user()->authorize('vendors_module_vendors_manage');

        $data['activePage'] = ['vendors' => 'vendors'];
        $data['breadcrumb'] = [
            ['title' => 'Vendors'],
            ['title' => 'Add Vendor'],
        ];
        return view('vendors::create-vendors',[
            'data' => $data,
        ]);
    }

    public function edit($id){
        \Auth::user()->authorize('vendors_module_vendors_manage');

        $vendor = \Modules\Vendors\Entities\Vendors::with(['created_by_user', 'user.province','type_of_vendor','type'])->whereId($id)->first();
        $data['activePage'] = ['vendors' => 'vendors'];
        $data['breadcrumb'] = [
            ['title' => 'Vendors'],
            ['title' => 'Edit Vendor Info'],
        ];
        $user = \Auth::user();
        if($user->hasRole('vendor')){
            $vendorsParent = \Modules\Vendors\Entities\Vendors::with('children')->where('user_id', $user->id)->first();
            $vendor_ids = $vendorsParent->children()->pluck('id');
            $vendor_ids->push($vendorsParent->id);
            $vendor = \Modules\Vendors\Entities\Vendors::whereId($id)->whereIn('id', $vendor_ids)->first();
            if(!$vendor){
                return abort(403);
            }
        }
        return view('vendors::edit-vendors',[
            'data' => $data,
            'vendor' => $vendor,
            'provinces' => \Modules\Core\Entities\CountryProvince::get(),
            'types' => \Modules\Vendors\Entities\TypeOFVendor::get(),
            'statuses' => \Modules\Vendors\Entities\VendorStatus::get(),
            'types_of_features' => \Modules\Core\Entities\TypeOfFeature::get()
            
        ]);
    }
    public function update(Request $request, $id){
        \Auth::user()->authorize('vendors_module_vendors_update');
        $request->validate([
            'address' => 'required',
            'type_id' => 'required',
            'province_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            
            $vendor = \Modules\Vendors\Entities\Vendors::whereId($id)->first();
            $user = \Modules\Users\Entities\User::whereId($vendor->user_id)->first();
            $user->address = $request->address;
            $user->province_id  = $request->province_id ;
            $user->Save();
           
            $vendor->company_name = $request->company_name;
            $starting_time = Carbon::parse($request->starting_time)->format('H:i');
            $closing_time = Carbon::parse($request->closing_time)->format('H:i');
            $vendor->starting_time = $request->starting_time;
            $vendor->closing_time = $request->closing_time;
            $vendor->status_id = $request->status_id;
            $vendor->location = $request->location;

            if($request->deactivated == 'false'){
                $vendor->deactivated = null;
            }else{
                $vendor->deactivated = now();
            }
            $vendor->type_id  = $request->type_id ;
            $vendor->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok', 'data' => $vendor]);
        
    }
    public function show($id){
        \Auth::user()->authorize('vendors_module_vendors_manage');

        return \Modules\Vendors\Entities\Vendors::whereId($id)->first();
    }
    public function Images($id){
        $vendor = \Modules\Vendors\Entities\Vendors::whereId($id)->first();
        $images = $vendor->getMedia('vendor-logo-image');
        $images_new  = collect([]);
        foreach($images as $image){
        $new['url'] =  url('/') . '/storage/app/public/' . $image->id . '/' . $image->file_name;
        $new['name'] = $image->file_name;
        $images_new->push($new);
        }
        return response()->json($images_new,200);
    }
    public function destroy(Request $request, $id){
        \Auth::user()->authorize('vendors_module_vendors_destroy');
        $vendor =  \Modules\Vendors\Entities\Vendors::whereId($id)->first();
        $vendor->delete();
        return response()->json('Ok',200);
    }

    public function UserVendor($id){
        \Auth::user()->authorize('vendors_module_vendors_manage');

        $user = \Modules\Users\Entities\User::whereId($id)->first();
        $data = \Modules\Vendors\Entities\Vendors::with('type')->where('user_id',$id)->first();
        return response()->json($data);
    }
    public function vendorProducts($id){
        \Auth::user()->authorize('vendors_module_vendors_manage');
        $products = \Modules\Products\Entities\Product::where('vendor_id', $id)->get();
        return response()->json($products);
    }

    public function addImage(Request $request){
        $id = $request->userId;
        $vendors = \Modules\Vendors\Entities\Vendors::whereId($id)->first();
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $extension = strtolower($request->file('file')->extension());
            $media_new_name = strtolower(md5(time())) . "." . $extension;
            $collection = "vendor-logo-image";
            
            $vendors->addMediaFromRequest('file')
                ->usingFileName($media_new_name)
                ->usingName($request->file('file')->getClientOriginalName())
                ->toMediaCollection($collection);
            return response()->json(['message' => 'ok']);
        }
    }
}
