<?php

namespace Modules\Drivers\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class DriversController extends Controller{
    public function __construct(){
        $this->middleware(['auth']);
    }  
    public function index(){
        $data = \Modules\Drivers\Entities\Driver::get();
        return response()->json($data);
    }
    public function manage(Request $request){
        \Auth::user()->authorize('drivers_module_drivers_manage');

        $data['activePage'] = ['drivers' => 'drivers'];
        $data['breadcrumb'] = [
            ['title' => 'Drivers'],
        ];
        // $data['addRecord'] = ['href' => route('Drivers.create')];
        if ($request->ajax()) {
            $data = \Modules\Drivers\Entities\Driver::with('created_by_user', 'user.province','type_of_driver','status')->select('*');
            return Datatables::of($data)
            ->addColumn('action', function ($driver) {
                $btn = '';
                $btn .= '<a data-action="edit" href=' .route('drivers.edit', $driver->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
                $btn .= '<a data-action="destroy" data-id='. $driver->id. '  class="btn btn-xs red p-2  tooltips"><i class="fa fa-times" aria-hidden="true"></i> </a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->filter(function ($query) use ($request) {
                if ($request->has('type_id') && $request->get('type_id') != null) {
                    $query->whereHas('type_of_driver', function($eloquent) use ($request){
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

        return view('drivers::drivers',[
            'data' => $data,
            'provinces' => \Modules\Core\Entities\CountryProvince::get(),
            'types' => \Modules\Drivers\Entities\DriverType::get()

        ]);
    }
    public function create(){
        \Auth::user()->authorize('drivers_module_drivers_manage');

        $data['activePage'] = ['drivers' => 'drivers'];
        $data['breadcrumb'] = [
            ['title' => 'Drivers'],
            ['title' => 'Add Driver'],
        ];
        return view('drivers::create-drivers',[
            'data' => $data,
        ]);
    }
    public function edit($id){
        \Auth::user()->authorize('drivers_module_drivers_manage');

        $driver = \Modules\Drivers\Entities\Driver::with(['created_by_user', 'user.province','type_of_driver'])->whereId($id)->first();
        $data['activePage'] = ['drivers' => 'drivers'];
        $data['breadcrumb'] = [
            ['title' => 'Drivers'],
            ['title' => 'Edit driver Info'],
        ];
        return view('drivers::edit-drivers',[
            'data' => $data,
            'driver' => $driver,
            'provinces' => \Modules\Core\Entities\CountryProvince::get(),
            'types' => \Modules\Drivers\Entities\DriverType::get(),
            'statuses' => \Modules\Drivers\Entities\DriverStatus::get()
        ]);
    }
    public function update(Request $request, $id){
        \Auth::user()->authorize('drivers_module_drivers_update');
        $request->validate([
            'address' => 'required',
            'type_id' => 'required',
            'province_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $driver = \Modules\Drivers\Entities\Driver::whereId($id)->first();
            $user = \Modules\Users\Entities\User::whereId($driver->user_id)->first();
            $user->address = $request->address;
            $user->province_id  = $request->province_id ;
            $user->Save();
           
            $driver->driving_license_ended = $request->driving_license_ended;
            $driver->driving_license_no = $request->driving_license_no;
            $driver->status_id = $request->status_id;
            $driver->location = $request->location;
            
            $driver->type_id  = $request->type_id ;
            $driver->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
        
    }
    public function destroy(Request $request, $id){
        \Auth::user()->authorize('drivers_module_drivers_destroy');
        $driver = \Modules\Drivers\Entities\Driver::whereId($id)->first();
        $driver->delete();
        return response()->json('Ok',200);
    }
    public function UserDriver($id){
        \Auth::user()->authorize('drivers_module_drivers_manage');

        $user = \Modules\Users\Entities\User::whereId($id)->first();
        $data = \Modules\Drivers\Entities\Driver::where('user_id',$id)->first();
        return response()->json($data);
    }
    public function addLicenseImage(Request $request ,$id){
        $driver = \Modules\Drivers\Entities\Driver::whereId($id)->first();
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $extension = strtolower($request->file('file')->extension());
            $media_new_name = strtolower(md5(time())) . "." . $extension;
            $collection = "driver-license-image";

            $driver->addMediaFromRequest('file')
                ->usingFileName($media_new_name)
                ->usingName($request->file('file')->getClientOriginalName())
                ->toMediaCollection($collection);
            return response()->json(['message' => 'ok']);
        }
    }

    public function removeLicenseImage(Request $request, $id){
        $image = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('file_name', $id)->first();
        $image->delete();
        return response()->json(['message' => 'ok']);
    } 
    
    public function show($id){
        $driver = \Modules\Drivers\Entities\Driver::whereId($id)->first();
        $images = $driver->getMedia('driver-license-image');
        $images_new  = collect([]);
        foreach($images as $image){
            $new['url'] =  url('/') . '/storage/app/public/' . $image->id . '/' . $image->file_name;
            $new['name'] = $image->file_name;
            $images_new->push($new);
        }
        return response()->json($images_new,200);
    }
    public function driverInfo($id){
        $driver = \Modules\Users\Entities\User::with('driver')->whereId($id)->first();
        // dd($driver->driver);
        return response()->json($driver->driver,200);

    }
}
