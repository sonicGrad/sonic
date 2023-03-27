<?php

namespace Modules\Drivers\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class DriversTypesController extends Controller{
    public function __construct(){
        $this->middleware(['auth']);
    }
    public function index(){
        $data = \Modules\Drivers\Entities\DriverType::get();
        return response()->json($data);
    } 
    public function manage(Request $request){
        \Auth::user()->authorize('drivers_module_drivers_types_manage');

        $data['activePage'] = ['drivers' => 'drivers_types'];
        $data['breadcrumb'] = [
            ['title' => 'Drivers Types'],
        ];
        $data['addRecord'] = ['href' => route('driver_types.create')];
        if ($request->ajax()) {
            $data = \Modules\Drivers\Entities\DriverType::select('*');
            return Datatables::of($data)
            ->addColumn('action', function ($driver_type) {
                $btn = '';
                $btn .= '<a data-action="edit" href=' .route('driver_types.edit', $driver_type->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
                $btn .= '<a data-action="destroy" data-id='. $driver_type->id. '  class="btn btn-xs red p-2  tooltips"><i class="fa fa-times" aria-hidden="true"></i> </a>';
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

        return view('drivers::drivers_types',[
            'data' => $data,
        ]);
    }
    public function create(){
        \Auth::user()->authorize('drivers_module_drivers_types_manage');

        $data['activePage'] = ['drivers' => 'drivers_types'];
        $data['breadcrumb'] = [
            ['title' => 'Add Driver Type'],
        ];
        return view('drivers::create-drivers_types',[
            'data' => $data,
        ]);
    }
    public function store(Request $request){
        \Auth::user()->authorize('drivers_module_drivers_types_store');

        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $type = \Modules\Drivers\Entities\DriverType::where('name', $request->name_en)->first();
            if($type){
                return response()->json(['message' =>'This Data Are Already Exisit'],403);
            }
            $type = \Modules\Drivers\Entities\DriverType::where('name', $request->name_ar)->first();
            if($type){
                return response()->json(['message' =>'هذه البيانات موجودة مسبقا'],403);
            }
            $type = new \Modules\Drivers\Entities\DriverType;
            $type
            ->setTranslation('name', 'en',  $request->name_en)
            ->setTranslation('name', 'ar',   $request->name_ar);
            $type->save();
            $type->created_by  = \Auth::user()->id;
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
        
    }
    public function edit($id){
        \Auth::user()->authorize('drivers_module_drivers_types_manage');

        $type = \Modules\Drivers\Entities\DriverType::whereId($id)->first();
        $data['activePage'] = ['drivers' => 'drivers_types'];
        $data['breadcrumb'] = [
            ['title' => 'Driver Type'],
            ['title' => 'Edit Driver Type'],
        ];
        return view('drivers::edit-drivers_types',[
            'data' => $data,
            'type' => $type
        ]);
    }
    public function update(Request $request, $id){
        \Auth::user()->authorize('drivers_module_drivers_types_update');

        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $type = \Modules\Drivers\Entities\DriverType::where('id','<>', $id)->where('name', $request->name_en)->first();
            if($type){
                return response()->json(['This Data Are Already Exisit'],403);
            }
            $type =  \Modules\Drivers\Entities\DriverType::whereId($id)->first();
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
        \Auth::user()->authorize('drivers_module_drivers_types_destroy');

        \Modules\Drivers\Entities\DriverType::destroy($id);
        return response()->json('Ok',200);
    }
    public function drivers($id){
       $drivers = \Modules\Drivers\Entities\Driver::where('type_id', $id)->get();
       return response()->json($drivers);
    } 
}
