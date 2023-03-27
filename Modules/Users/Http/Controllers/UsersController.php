<?php

namespace Modules\Users\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Yajra\Datatables\Datatables;

class UsersController extends Controller{

    public function __construct(){
        $this->middleware(['auth']);
    }

    public function manage(Request $request){
        \Auth::user()->authorize('users_module_users_manage');

        $data['activePage'] = ['users' => 'users'];
        $data['breadcrumb'] = [
            ['title' => "Users Management"],
            ['title' => 'Users'],
        ];
        $user =\Auth::user();
        $data['addRecord'] = ['href' => route('users.create')];
        if ($request->ajax()) {
            if($user->hasRole('vendor') && $user->hasRole('main branch supplier')){
                $vendor = \Modules\Vendors\Entities\Vendors::with(['user','parent'])->where('user_id', $user->id)->first();
                // $data = \Modules\Vendors\Entities\Vendors::with(['user','parent'])
                // ->where('parent_id', $vendor->id);
                $user_ids = collect([]);
                $users = $vendor->children()->get();
                $user_ids->push($user->id);
                foreach ($users as $user_id) {
                    $user_ids->push($user_id->user_id);
                }
                $data = \Modules\Users\Entities\User::with(['province', 'created_by','status'])
                ->whereIn('id', $user_ids)->select('*');
                // return $user_ids;
            }else{
                $data = \Modules\Users\Entities\User::with(['province', 'created_by','status'])->select('*');
            }
            return Datatables::of($data)
            ->addColumn('action', function ($user) {
                $btn = '';
                $btn .= '<a data-action="edit" href=' .route('users.edit', $user->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
                $btn .= '<a data-action="destroy" data-id='. $user->id. '  class="btn btn-xs red p-2  tooltips"><i class="fa fa-times" aria-hidden="true"></i> </a>';
                $btn .= '<a data-action="change-pass" data-id='. $user->id. ' title="change password for users" style="margin-left :5px" class="btn btn-xs btn-secondary"><i class="ace-icon fa  fa-key"></i> </a>';
                return $btn;
            })
            ->addColumn('roles', function ($user) {
                $value = '';
                foreach($user->roles as $role){
                       $value .= '<label class="badge badge-primary ml-2">' . $role->name  .'</label>';
                }
                return $value;
            })
            ->rawColumns(['action', 'roles'])
            ->filter(function ($query) use ($request) {
                if ($request->has('full_name') && $request->get('full_name') != null) {
                    $query->where('full_name', 'like', "%{$request->get('full_name')}%");
                }
                if ($request->has('national_id') && $request->get('national_id') != null) {
                    $query->where('national_id', 'like', "%{$request->get('national_id')}%");
                }
                if ($request->has('mobile_no') && $request->get('mobile_no') != null) {
                    $query->where('mobile_no', 'like', "%{$request->get('mobile_no')}%");
                }
                if ($request->has('email') && $request->get('email') != null) {
                    $query->where('email', 'like', "%{$request->get('email')}%");
                }
                if ($request->has('province_id') && $request->get('province_id') != null) {
                    $query->whereHas('province', function($eloquent) use ($request){
                        $eloquent->whereId(trim($request->get('province_id')));
                    });
                }
                if ($request->has('role_name') && $request->get('role_name') != null) {
                   $query->role($request->get('role_name')); 
                }
                if ($request->has('created_at') && $request->get('created_at') != null) {
                   $query->WhereCreatedAt($request->get('created_at')); 
                }
            })
            ->toJson();
            // ->make(true);
        }

        return view('users::users',[
            'data' => $data,
            'provinces' => \Modules\Core\Entities\CountryProvince::get(),
            'roles' => \Spatie\Permission\Models\Role::whereNull('parent_id')->get()
        ]);
    }

    public function create(){
        \Auth::user()->authorize('users_module_users_manage');

        $data['activePage'] = ['users' => 'users'];
        $data['breadcrumb'] = [
            ['title' => "Users Management"],
            ['title' => "Users"],
            ['title' => 'Add User'],
        ];
        $provinces = \Modules\Core\Entities\CountryProvince::get(); 
        $roles = \Spatie\Permission\Models\Role::whereNull('parent_id')->get();
        $user =\Auth::user();
        if($user->hasRole('vendor')){
            return view('users::create-user-by-vendor',[
                'data' => $data,
                'provinces' => \Modules\Core\Entities\CountryProvince::get(),
            ]); 
        }
        return view('users::create-user',[
            'provinces' => $provinces,
            'data' => $data,
            'roles' => $roles,
            'types_of_features' => \Modules\Core\Entities\TypeOfFeature::get()

        ]);
    }

    public function store(Request $request){
        \Auth::user()->authorize('users_module_users_store');

        $request->validate([
            'national_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'mobile_no' => 'required',
            'email' => 'required',
            'province_id' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $mobile_no = \Modules\Users\Entities\User::where('mobile_no', $request->mobile_no)->first();
            if($mobile_no){
                return response()->json(['message' =>'This Mobile Are Already Exisit'],403);
            }
            $email = \Modules\Users\Entities\User::where('email', $request->email)->first();
            if($email){
                return response()->json(['message' =>'This Email Are Already Exisit'],403);
            }
            $password = rand(11111111, 9999999);
            $user = new \Modules\Users\Entities\User;
            $user->national_id = $request->national_id;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->address = $request->address;
            $user->mobile_no = $request->mobile_no;
            $user->email = $request->email;
            $user->province_id = $request->province_id;
            $user->password = Hash::make($password);
            $user->created_by = \Auth::id();
            $user->assignRole($request->role_id);
            if(isset($request->sub_role) && count($request->sub_role) > 0){
                foreach ($request->sub_role as $sub) {
                    $user->assignRole($sub);
                }
            }
            $user->save();
            $otp = new \Modules\Users\Entities\Otp;
            $otp->mobile_no  =  $request->mobile_no;
            $otp->code  =  $password;
            $otp->user_id  =  $user->id;
            $otp->message = 'Hi ' . $user->full_name.  ' your Password is ' . $password;
            $otp->created_by = \Auth::id();
            $otp->save();
            if($request->role_id == 'vendor'){
                if(!$request->type_id){
                    return response()->json(['message' =>"please Enter Type of Vendor"],403);
                }
                if(!$request->company_name){
                    return response()->json(['message' =>"please Enter Company Name of Vendor"],403);
                }

                $vendor = new  \Modules\Vendors\Entities\Vendors;
                $vendor->created_by = \Auth::user()->id;
                $vendor->user_id = $user->id;
                $vendor->type_id = $request->type_id;
                $vendor->company_name = $request->company_name;
                $starting_time = Carbon::parse($request->starting_time)->format('H:i');
                $closing_time = Carbon::parse($request->closing_time)->format('H:i');
                $vendor->starting_time = $starting_time;
                $vendor->closing_time = $closing_time;
                $vendor->status_id  = $request->status_id ;
                $vendor->location  = $request->location ;


                $vendor->save();
                \DB::commit();

                return response()->json($vendor);
            }
            if($request->role_id == 'driver'){
                if(!$request->type_id){
                    return response()->json(['message' =>"please Enter Type of driver"],403);
                }
                if(!$request->driving_license_no){
                    return response()->json(['message' =>"please Enter Driving License Number of driver"],403);
                }
                if(!$request->driving_license_ended){
                    return response()->json(['message' =>"please Enter Driving License Ended Date of driver"],403);
                }
                if(!$request->driving_license_image){
                    return response()->json(['message' =>"please Enter Driving License Image of driver"],403);
                }
                $driver = new  \Modules\Drivers\Entities\Driver;
                $driver->created_by = \Auth::user()->id;
                $driver->user_id = $user->id;
                $driver->type_id = $request->type_id;
                $driver->driving_license_no = $request->driving_license_no;
                $driver->driving_license_ended = $request->driving_license_ended;
                $driver->status_id  = $request->status_id ;
                $driver->location  = $request->location ;

                if ($request->hasFile('driving_license_image') && $request->file('driving_license_image')->isValid()) {
                    $extension = strtolower($request->file('driving_license_image')->extension());
                    $media_new_name = strtolower(md5(time())) . "." . $extension;
                    $collection = "driver-license-image";
        
                    $driver->addMediaFromRequest('driving_license_image')
                        ->usingFileName($media_new_name)
                        ->usingName($request->file('driving_license_image')->getClientOriginalName())
                        ->toMediaCollection($collection);
                }
                $driver->save();
             }
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
        
    }
    public function changePasswordForUser($id){
        \Auth::user()->authorize('users_module_users_change_password');

        $password = rand(11111111, 9999999);
        $user = \Modules\Users\Entities\User::whereId($id)->first();
        $user->password = Hash::make($password);
        $user->save();
        $otp = new \Modules\Users\Entities\Otp;
        $otp->mobile_no  =  $user->mobile_no;
        $otp->code  =  $password;
        $otp->user_id  =  $user->id;
        $otp->message = 'Hi ' . $user->full_name.  ' your Password is ' . $password;
        $otp->created_by = \Auth::id();
        $otp->save();
    }
    public function storeForVendor(Request $request){
        \Auth::user()->authorize('users_module_users_store');
        $request->validate([
            'national_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'mobile_no' => 'required',
            'email' => 'required',
            'maximum_distance' => 'required',
            'province_id' => 'required',
        ]);
        $user = \Auth::user();
        \DB::beginTransaction();
        try {
            $mobile_no = \Modules\Users\Entities\User::where('mobile_no', $request->mobile_no)->first();
            if($mobile_no){
                return response()->json(['message' =>'This Mobile Are Already Exisit'],403);
            }
            $email = \Modules\Users\Entities\User::where('email', $request->email)->first();
            if($email){
                return response()->json(['message' =>'This Email Are Already Exisit'],403);
            }
            $password = rand(11111111, 9999999);
            $user = new \Modules\Users\Entities\User;
            $user->national_id = $request->national_id;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->province_id = $request->province_id;
            $user->address = $request->address;
            $user->mobile_no = $request->mobile_no;
            $user->password = \Hash::make($password);
            $user->email = $request->email;
            $user->save();
            $otp = new \Modules\Users\Entities\Otp;
            $otp->mobile_no  =  $request->mobile_no;
            $otp->code  =  $password;
            $otp->user_id  =  $user->id;
            $otp->message = 'Hi ' . $user->full_name.  ' your Password is ' . $password;
            $otp->created_by = \Auth::id();
            $otp->save();
            $role = \Spatie\Permission\Models\Role::whereId('7')->first();
            $user->assignRole($role->name);
            $user->assignRole('vendor');
            $user->status_id = '1';
            $user->save();
                $vendor = new  \Modules\Vendors\Entities\Vendors;
                $vendor->created_by = \Auth::user()->id;
                $parent_user = \Auth::user();
                $parent_vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $parent_user->id)->first();
                $vendor->user_id = $user->id;
                $vendor->type_id = $parent_vendor->type_id;
                $vendor->parent_id = $parent_vendor->id;
                $vendor->company_name = $request->company_name;
                $starting_time = Carbon::parse($request->starting_time)->format('H:i');
                $closing_time = Carbon::parse($request->closing_time)->format('H:i');
                $vendor->starting_time = $starting_time;
                $vendor->closing_time = $closing_time;
                $vendor->status_id  ='1' ;
                $vendor->maximum_distance = $request->maximum_distance;
                $vendor->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
    }
    
    public function edit($id){
        \Auth::user()->authorize('users_module_users_manage');

        $data['activePage'] = ['users' => 'users'];
        $data['breadcrumb'] = [
            ['title' => "Users Management"],
            ['title' => "Users"],
            ['title' => 'Edit User'],
        ];
        $user =\Auth::user();
        if($user->hasRole('vendor')){
            
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id',$id)->first();
            $parent = \Modules\Vendors\Entities\Vendors::where('user_id', '<>', \Auth::user()->id)->where('parent_id',$vendor->parent_id)->first();
           if($parent->user_id == $user->id){
            return response()->json(['message' => 'Not Allow'], 403);
           }
            return view('users::edit-user-by-vendor',[
                'data' => $data,
                'provinces' => \Modules\Core\Entities\CountryProvince::get(),
                'user' => \Modules\Users\Entities\User::whereId($id)->first(),
                'vendor' => $vendor,
                'statuses' => \Modules\Vendors\Entities\VendorStatus::get()
            ]); 
        }
        $provinces = \Modules\Core\Entities\CountryProvince::get(); 
        $roles = \Spatie\Permission\Models\Role::whereNull('parent_id')->get();
        $user = \Modules\Users\Entities\User::whereId($id)->first();
        $withoutArray = \DB::table('um_model_has_roles')
        ->where('model_id', $id)
        ->where('model_type','Modules\Users\Entities\User')
        ->pluck('role_id');
        $subRoles =  $withoutArray->toArray();
        // return array_slice($subRoles,1);
        return view('users::edit-user',[
            'data' => $data,
            'provinces' => $provinces, 
            'roles' => $roles,
            'user' => $user,
            'user_role' => $user->roles[0],
            'sub_roles' => count(array_slice($subRoles,1)) != 0 ? $withoutArray : $withoutArray
        ]);
    }
    public function UpdateForVendor(Request $request,$id){
        \Auth::user()->authorize('users_module_users_update');
        $request->validate([
            'national_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'mobile_no' => 'required',
            'email' => 'required',
            'province_id' => 'required',
            'maximum_distance' => 'required',
        ]);
        $user = \Auth::user();
        \DB::beginTransaction();
        try {
            $mobile_no = \Modules\Users\Entities\User::where('id', '<>', $id)->where('mobile_no', $request->mobile_no)->first();
            if($mobile_no){
                return response()->json(['message' =>'This Mobile Are Already Exisit'],403);
            }
            $email = \Modules\Users\Entities\User::where('id', '<>', $id)->where('email', $request->email)->first();
            if($email){
                return response()->json(['message' =>'This Email Are Already Exisit'],403);
            }
            $user =  \Modules\Users\Entities\User::whereId($id)->first();
            $user->national_id = $request->national_id;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->province_id = $request->province_id;
            $user->address = $request->address;
            $user->mobile_no = $request->mobile_no;
            $user->email = $request->email;
            $role = \Spatie\Permission\Models\Role::whereId('7')->first();
            $user->assignRole($role->name);
            $user->assignRole('vendor');
            $user->save();

            $parent_user = \Auth::user();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
                if(!$vendor){
                    $vendor = new  \Modules\Vendors\Entities\Vendors;
                    $vendor->created_by = \Auth::user()->id;
                    $vendor->parent_id = \Auth::user()->id;
                    $vendor->type_id = \Auth::user()->type_id;
                }
                $parent_vendor = \Modules\Vendors\Entities\Vendors::where('user_id', \Auth::user()->id)->first();
                $vendor->user_id = $user->id;
                $vendor->type_id = $vendor->type_id;
                $vendor->company_name = $request->company_name;
                $user->hasRole('main branch supplier') ? null : $vendor->parent_id = $parent_vendor->id;
                $starting_time = Carbon::parse($request->starting_time)->format('H:i');
                $closing_time = Carbon::parse($request->closing_time)->format('H:i');
                $vendor->starting_time = $starting_time;
                $vendor->closing_time = $closing_time;
                $vendor->maximum_distance = $request->maximum_distance;
                $vendor->status_id   = $request->status_id  ;
                $vendor->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);  
    }
    public function update(Request $request, $id){
        \Auth::user()->authorize('users_module_users_update');
        $request->validate([
            'national_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'address' => 'required',
            'mobile_no' => 'required',
            'email' => 'required',
            'province_id' => 'required',
        ]);
        $user = \Auth::user();
        \DB::beginTransaction();
        try {
            $mobile_no = \Modules\Users\Entities\User::where('id', '<>', $id)->where('mobile_no', $request->mobile_no)->first();
            if($mobile_no){
                return response()->json(['message' =>'This Mobile Are Already Exisit'],403);
            }
            $email = \Modules\Users\Entities\User::where('id', '<>', $id)->where('email', $request->email)->first();
            if($email){
                return response()->json(['message' =>'This Email Are Already Exisit'],403);
            }
            $user =  \Modules\Users\Entities\User::whereId($id)->first();
            $user->national_id = $request->national_id;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->address = $request->address;
            $user->mobile_no = $request->mobile_no;
            $user->email = $request->email;
            $role = \DB::table('um_model_has_roles')->where('model_id', $user->id)->where('model_type','Modules\Users\Entities\User')->delete();
            $user->assignRole($request->role_id);
            if(isset($request->sub_id) && count($request->sub_id ) > 0){
                foreach ($request->sub_id as $sub) {
                    $user->assignRole($sub);
                }
            }
            $user->province_id = $request->province_id;
            $user->created_by = \Auth::id();
            $user->save();
            if($request->role_id == 'vendor'){
                if(!$request->type_id){
                    return response()->json(['message' =>"please Enter Type of Vendor"],403);
                }
                $vendor =  \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
                if(!$vendor){
                    $vendor = new  \Modules\Vendors\Entities\Vendors;
                    $vendor->created_by = \Auth::user()->id;
                }
                $vendor->user_id = $user->id;
                $vendor->type_id = $request->type_id;
                $vendor->company_name = $request->company_name;
                $starting_time = Carbon::parse($request->starting_time)->format('H:i');
                $closing_time = Carbon::parse($request->closing_time)->format('H:i');
                $vendor->starting_time = $starting_time;
                $vendor->closing_time = $closing_time;
                $vendor->status_id  = $request->status_id ;
                $vendor->location  = $request->location ;
                $vendor->save();
            }
            if($request->role_id == 'driver'){
                if( !$request->type_id){
                    return response()->json(['message' =>"please Enter Type of Driver"],403);
                }
                $driver =  \Modules\Drivers\Entities\Driver::where('user_id', $user->id)->first();
                if(!$driver){
                    $driver = new  \Modules\Drivers\Entities\Driver;
                    $driver->created_by = \Auth::user()->id;
                }
                $driver->user_id = $user->id;
                $driver->type_id = $request->type_id;
                $driver->driving_license_no = $request->driving_license_no;
                $driver->driving_license_ended = $request->driving_license_ended;
                $driver->status_id  = $request->status_id ;
                $driver->location  = $request->location ;
                $driver->save();
            }
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
        
    }
    
    public function destroy(Request $request, $id){
        \Auth::user()->authorize('users_module_users_destroy');
        $user =  \Modules\Users\Entities\User::whereId($id)->first();
        $user->delete();
        return response()->json('Ok',200);
    }
    
    public function ChangePassword(Request $request){
        \Auth::user()->authorize('users_module_users_change_password');

        $user = \Auth::user();
        $request->validate([
            'current_pass' => 'required',
            'password' => 'required',
            'password_confirmation' => 'required_with:password|same:password'
        ]);
        $current = Hash::check($request->current_pass, $user->password);
        
        if($current == false){
            return response()->json('Your Password is not Correct',403);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json('Ok',200);

    }
    

    
}
