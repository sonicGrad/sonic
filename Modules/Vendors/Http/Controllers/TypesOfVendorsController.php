<?php

namespace Modules\Vendors\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class TypesOfVendorsController extends Controller{
    public function __construct(){
        $this->middleware(['auth']);
    }  
    public function index(){
        $data = \Modules\Vendors\Entities\TypeOFVendor::get();
        return response()->json($data);
    } 
    public function manage(Request $request){
        \Auth::user()->authorize('vendors_module_vendors_types_manage');

        $data['activePage'] = ['vendors' => 'vendors_types'];
        $data['breadcrumb'] = [
            ['title' => 'Vendor Types'],
        ];
        $data['addRecord'] = ['href' => route('vendor_types.create')];
        if ($request->ajax()) {
            $data = \Modules\Vendors\Entities\TypeOFVendor::select('*');
            return Datatables::of($data)
            ->addColumn('action', function ($vendors_type) {
                $btn = '';
                $btn .= '<a data-action="edit" href=' .route('vendor_types.edit', $vendors_type->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
                $btn .= '<a data-action="destroy" data-id='. $vendors_type->id. '  class="btn btn-xs red p-2  tooltips"><i class="fa fa-times" aria-hidden="true"></i> </a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->filter(function ($query) use ($request) {
                if ($request->has('name_en') && $request->get('name_en') != null) {
                    $query->where('name->en', 'like', "%{$request->get('name_en')}%");
                }
                if ($request->has('name_ar') && $request->get('name_ar') != null) {
                    $query->where('name->ar', 'like', "%{$request->get('name_ar')}%");
                }
            })
            ->toJson();
        }

        return view('vendors::vendors_types',[
            'data' => $data,
        ]);
    }
    public function create(){
        \Auth::user()->authorize('vendors_module_vendors_types_manage');

        $data['activePage'] = ['vendors' => 'vendors_types'];
        $data['breadcrumb'] = [
            ['title' => 'Vendor Types'],
            ['title' => 'Add Vendor Type'],
        ];
        return view('vendors::create-vendors_types',[
            'data' => $data,
        ]);
    }

    
    public function store(Request $request){
        \Auth::user()->authorize('vendors_module_vendors_types_store');

        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $type = \Modules\Vendors\Entities\TypeOFVendor::where('name->en', $request->name_en)->first();
            if($type){
                return response()->json(['message' =>'This Data Are Already Exisit'],403);
            }
            $type = \Modules\Vendors\Entities\TypeOFVendor::where('name->ar', $request->name_ar)->first();
            if($type){
                return response()->json(['message' =>'This Data Are Already Exisit'],403);
            }
            $type = new \Modules\Vendors\Entities\TypeOFVendor;
            $type
            ->setTranslation('name', 'en',  $request->name_en)
            ->setTranslation('name', 'ar',   $request->name_ar);
            $type->created_by  = \Auth::user()->id;
            $type->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok','data' => $type]);
        
    }
    public function show($id){
        $product = \Modules\Vendors\Entities\TypeOFVendor::whereId($id)->first();
        $images = $product->getMedia('vendor-type-image');
        $images_new  = collect([]);
        foreach($images as $image){
            $new['url'] =  url('/') . '/storage/app/public/' . $image->id . '/' . $image->file_name;
            $new['name'] = $image->file_name;
            $images_new->push($new);
        }
        return response()->json($images_new,200);
    }
    public function edit($id){
        \Auth::user()->authorize('vendors_module_vendors_types_manage');

        $type = \Modules\Vendors\Entities\TypeOFVendor::whereId($id)->first();
        $data['activePage'] = ['vendors' => 'vendors_types'];
        $data['breadcrumb'] = [
            ['title' => 'Vendor Types'],
            ['title' => 'Edit Vendor Type'],
        ];
        return view('vendors::edit-vendors_types',[
            'data' => $data,
            'type' => $type
        ]);
    }
    public function update(Request $request, $id){
        \Auth::user()->authorize('vendors_module_vendors_types_update');

        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $type = \Modules\Vendors\Entities\TypeOFVendor::where('id','<>', $id)->where('name->en', $request->name_en)->first();
            if($type){
                return response()->json(['This Data Are Already Exisit'],403);
            }
            $type = \Modules\Vendors\Entities\TypeOFVendor::where('id','<>', $id)->where('name->ar', $request->name_ar)->first();
            if($type){
                return response()->json(['This Data Are Already Exisit'],403);
            }
            $type =  \Modules\Vendors\Entities\TypeOFVendor::whereId($id)->first();
            $type
            ->setTranslation('name', 'en',  $request->name_en)
            ->setTranslation('name', 'ar',   $request->name_ar);
            $type->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
        
    }
    public function destroy(Request $request, $id){
        \Auth::user()->authorize('vendors_module_vendors_types_destroy');

        \Modules\Vendors\Entities\TypeOFVendor::destroy($id);
        return response()->json('Ok',200);
    }
    public function vendors($id){
       $vendors = \Modules\Vendors\Entities\Vendors::where('type_id', $id)->get();
       return response()->json($vendors);
    }
    public function addImage(Request $request){
        $id = $request->userId;

        $category = \Modules\Vendors\Entities\TypeOFVendor::whereId($id)->first();
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $extension = strtolower($request->file('file')->extension());
            $media_new_name = strtolower(md5(time())) . "." . $extension;
            $collection = "vendor-type-image";

            $category->addMediaFromRequest('file')
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
}
