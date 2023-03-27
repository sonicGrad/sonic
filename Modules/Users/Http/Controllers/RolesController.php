<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class RolesController extends Controller{
    public function __construct(){
        $this->middleware(['auth']);
    } 
     
    public function manage(Request $request){
        \Auth::user()->authorize('users_module_roles_manage');

        $data['activePage'] = ['users' => 'roles'];
        $data['breadcrumb'] = [
            ['title' => 'Roles'],
        ];
        $data['addRecord'] = ['href' => route('roles.create')];
        if ($request->ajax()) {
            $data = \Spatie\Permission\Models\Role::select('*');
            return Datatables::of($data)
            ->addColumn('action', function ($role) {
                $btn = '';
                $btn .= '<a data-action="edit" href=' .route('roles.edit', $role->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
                $btn .= '<a data-action="destroy" data-id='. $role->id. '  class="btn btn-xs red p-2  tooltips"><i class="fa fa-times" aria-hidden="true"></i> </a>';
                $btn .= '<a class="btn btn-falcon-default btn-sm mr-2" href="' . url('/'). '/admin/permissions/manage?type=role&id=' .$role->id . '" title="إدارة الصلاحيات"><span class="fas fa-fingerprint" data-fa-transform="shrink-3"></span></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->filter(function ($query) use ($request) {
                if ($request->has('name') && $request->get('name') != null) {
                    $query->where('name', 'like', "%{$request->get('name')}%");
                }
            })
            ->toJson();
        }

        return view('users::roles',[
            'data' => $data,
        ]);
    }
    public function create(){
        \Auth::user()->authorize('users_module_roles_manage');

        $data['activePage'] = ['users' => 'roles'];
        $data['breadcrumb'] = [
            ['title' => 'Add Role'],
        ];
        return view('users::create-role',[
            'data' => $data,
            'parentRoles' => \Spatie\Permission\Models\Role::whereNull('parent_id')->get()
        ]);
    }

    public function store(Request $request){
        \Auth::user()->authorize('users_module_roles_store');

        $request->validate([
            'name' => 'required',
            'label' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $role = \Spatie\Permission\Models\Role::where('name', $request->name)->first();
            if($role){
                return response()->json(['This Role Are Already Exisit'],403);
            }
            $role = new \Spatie\Permission\Models\Role;
            $role->name = $request->name;
            $role->label = $request->label;
            $role->parent_id = $request->parent_id;
            $role->save();
            // $user->
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
        
    }
    public function edit($id){
        \Auth::user()->authorize('users_module_roles_manage');

        $role = \Spatie\Permission\Models\Role::whereId($id)->first();
        $data['activePage'] = ['users' => 'roles'];
        $data['breadcrumb'] = [
            ['title' => 'Edit Role'],
        ];
        return view('users::edit-role',[
            'data' => $data,
            'role' => $role,
            'parentRoles' => \Spatie\Permission\Models\Role::whereNull('parent_id')->get()
        ]);
    }
    public function update(Request $request,$id){
        \Auth::user()->authorize('users_module_roles_update');

        $request->validate([
            'name' => 'required',
            'label' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $role = \Spatie\Permission\Models\Role::where('name',  '<>',$request->name)->where('name', $request->name)->first();
            if($role){
                return response()->json(['This Role Are Already Exisit'],403);
            }
            $role =  \Spatie\Permission\Models\Role::whereId($id)->first();
            $role->name = $request->name;
            $role->label = $request->label;
            $role->parent_id = $request->parent_id;
            $role->save();
            // $user->
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
        
    }
    public function destroy(Request $request, $id){
        \Auth::user()->authorize('users_module_roles_destroy');

        \Spatie\Permission\Models\Role::destroy($id);
        return response()->json('Ok',200);
    }

    public function subRoles($id){
        \Auth::user()->authorize('users_module_roles_manage');
        $parent = \Spatie\Permission\Models\Role::where('name', $id)->first();
        $roles= \Spatie\Permission\Models\Role::whereNotNull('parent_id')->where('parent_id', $parent->id)->get();
        return response()->json($roles);
    }
   
    

    
}
