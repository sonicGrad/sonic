<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class UsersStatusController extends Controller{
    public function __construct(){
        $this->middleware(['auth']);
    }
    public function index(){
        $data = \Modules\Users\Entities\UserStatus::get();
        return response()->json($data);
    }
    public function manage(Request $request){
        \Auth::user()->authorize('users_module_user_status_manage');

        $data['activePage'] = ['users' => 'user_status'];
        $data['breadcrumb'] = [
            ['title' => 'User Status'],
        ];
        $data['addRecord'] = ['href' => route('user_status.create')];
        if ($request->ajax()) {
            $data = \Modules\Users\Entities\UserStatus::select('*');
            return Datatables::of($data)
            ->addColumn('action', function ($user_status) {
                $btn = '';
                $btn .= '<a data-action="edit" href=' .route('user_status.edit', $user_status->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
                $btn .= '<a data-action="destroy" data-id='. $user_status->id. '  class="btn btn-xs red p-2  tooltips"><i class="fa fa-times" aria-hidden="true"></i> </a>';
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

        return view('users::user_status',[
            'data' => $data,
        ]);
    }
    public function create(){
        \Auth::user()->authorize('users_module_user_status_manage');

        $data['activePage'] = ['users' => 'user_status'];
        $data['breadcrumb'] = [
            ['title' => 'User Status'],
            ['title' => 'Add User Status'],
        ];
        return view('users::create-user_status',[
            'data' => $data,
        ]);
    }
    public function store(Request $request){
        \Auth::user()->authorize('users_module_user_status_store');

        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $type = \Modules\Users\Entities\UserStatus::where('name->en', $request->name_en)->first();
            if($type){
                return response()->json(['message' =>'This Data Are Already Exisit'],403);
            }
            $type = \Modules\Users\Entities\UserStatus::where('name->ar', $request->name_ar)->first();
            if($type){
                return response()->json(['message' =>'هذه البيانات موجودة مسبقا'],403);
            }
            $type = new \Modules\Users\Entities\UserStatus();
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

        return response()->json(['message' => 'ok']);
        
    }
    public function edit($id){
        \Auth::user()->authorize('users_module_user_status_manage');

        $type = \Modules\Users\Entities\UserStatus::whereId($id)->first();
        $data['activePage'] = ['users' => 'user_status'];
        $data['breadcrumb'] = [
            ['title' => 'User Status'],
            ['title' => 'Edit User Status'],
        ];
        return view('users::edit-user_status',[
            'data' => $data,
            'type' => $type
        ]);
    }
    public function update(Request $request, $id){
        \Auth::user()->authorize('users_module_user_status_update');

        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $type = \Modules\Users\Entities\UserStatus::where('id','<>', $id)->where('name->en', $request->name_en)->first();
            if($type){
                return response()->json(['This Data Are Already Exisit'],403);
            }
            $type = \Modules\Users\Entities\UserStatus::where('id','<>', $id)->where('name->ar', $request->name_en)->first();
            if($type){
                return response()->json(['This Data Are Already Exisit'],403);
            }
            $type =  \Modules\Users\Entities\UserStatus::whereId($id)->first();
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
        \Auth::user()->authorize('users_module_user_status_destroy');

        \Modules\Users\Entities\UserStatus::destroy($id);
        return response()->json('Ok',200);
    }
}
