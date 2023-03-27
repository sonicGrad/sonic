<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class CountriesProvincesController extends Controller{
    public function __construct(){
        $this->middleware(['auth']);
    }
    public function manage(Request $request){
        \Auth::user()->authorize('core_module_county_province_manage');

        $data['activePage'] = ['core' => 'county_province'];
        $data['breadcrumb'] = [
            ['title' => 'Country Province'],
        ];
        $data['addRecord'] = ['href' => route('county_province.create')];
        if ($request->ajax()) {
            $data = \Modules\Core\Entities\CountryProvince::select('*');
            return Datatables::of($data)
            ->addColumn('action', function ($vendors_type) {
                $btn = '';
                $btn .= '<a data-action="edit" href=' .route('county_province.edit', $vendors_type->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
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

        return view('core::county_province',[
            'data' => $data,
        ]);
    }
    public function create(){
        \Auth::user()->authorize('core_module_county_province_manage');

        $data['activePage'] = ['core' => 'county_province'];
        $data['breadcrumb'] = [
            ['title' => 'Country Province'],
            ['title' => 'Add Country Province Info'],
        ];
        return view('core::create-county_province',[
            'data' => $data,
        ]);
    }
    public function store(Request $request){
        \Auth::user()->authorize('core_module_county_province_store');
        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
        ]);
        
        \DB::beginTransaction();
        try {
            $type = \Modules\Core\Entities\CountryProvince::where('name->en', $request->name_en)->first();
            if($type){
                return response()->json(['message' =>'This Data Are Already Exisit'],403);
            }
            $type = \Modules\Core\Entities\CountryProvince::where('name->ar', $request->name_ar)->first();
            if($type){
                return response()->json(['message' =>'This Data Are Already Exisit'],403);
            }
            $type = new \Modules\Core\Entities\CountryProvince;
            $type
            ->setTranslation('name', 'en',  $request->name_en)
            ->setTranslation('name', 'ar',   $request->name_ar);
            $type->country_id  = '1';
            $type->created_by  = \Auth::user()->id;
            $type->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
        
    } 
    public function edit($id){
        \Auth::user()->authorize('core_module_county_province_manage');

        $county_province = \Modules\Core\Entities\CountryProvince::with('created_by_user')->whereId($id)->first();
        $data['activePage'] = ['core' => 'county_provinc'];
        $data['breadcrumb'] = [
            ['title' => 'Country Provinces'],
            ['title' => 'Edit Country Province Info'],
        ];
        return view('core::edit-county_province',[
            'data' => $data,
            'county_province' => $county_province,
        ]);
    }
     
    public function update(Request $request, $id){
        \Auth::user()->authorize('core_module_county_province_update');

        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $type = \Modules\Core\Entities\CountryProvince::where('id', '<>', $id)->where('name->en', $request->name_en)->first();
            if($type){
                return response()->json(['message' =>'This Data Are Already Exisit'],403);
            }
            $type = \Modules\Core\Entities\CountryProvince::where('id', '<>', $id)->where('name->ar', $request->name_ar)->first();
            if($type){
                return response()->json(['message' =>'This Data Are Already Exisit'],403);

            }
            $type =  \Modules\Core\Entities\CountryProvince::whereId($id)->first();
            $type
            ->setTranslation('name', 'en',  $request->name_en)
            ->setTranslation('name', 'ar',   $request->name_ar);
            $type->country_id  = '1';
            $type->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
        
    }
    public function destroy(Request $request, $id){
        \Auth::user()->authorize('core_module_county_province_destroy');

        \Modules\Core\Entities\CountryProvince::destroy($id);
        return response()->json('Ok',200);
    }
}
