<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class ArchiveOtpsController extends Controller{
    public function __construct(){
        $this->middleware(['auth']);
    }
    public function manage(Request $request){
        \Auth::user()->authorize('users_module_otps_manage');

        $data['activePage'] = ['users' => 'otps'];
        $data['breadcrumb'] = [
            ['title' => "Users Management"],
            ['title' => 'SMS Messages'],
        ];
        $user =\Auth::user();
        // $data['addRecord'] = ['href' => route('users.create')];
        if ($request->ajax()) {
            $data = \Modules\Users\Entities\Otp::select('*');
            return Datatables::of($data)
            ->filter(function ($query) use ($request) {
                if ($request->has('mobile_no') && $request->get('mobile_no') != null) {
                    $query->where('mobile_no', 'like', "%{$request->get('mobile_no')}%");
                }
            })
            ->toJson();
            // ->make(true);
        }

        return view('users::otps',[
            'data' => $data,
        ]);
    }
}
