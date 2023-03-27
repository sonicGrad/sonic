<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class CountriesController extends Controller{
    public function __construct(){
        $this->middleware(['auth']);
    }

    public function manage(Request $request){
        \Auth::user()->authorize('core_module_countries_manage');

        $data['activePage'] = ['core' => 'countries'];
        $data['breadcrumb'] = [
            ['title' => 'Countries'],
        ];

        if ($request->ajax()) {
            $data = \Modules\Core\Entities\Country::select('*');
            return Datatables::of($data)
            ->addColumn('action', function ($user) {
                $btn = '';
                $btn .= '<a href="#edit-'.$user->id.'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                $btn .= '<a href="#delete-'.$user->id.'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
            })
            ->filter(function ($query) use ($request) {
                if ($request->has('name')) {
                    $query->where('name', 'like', "%{$request->get('name')}%");
                }
    
                if ($request->has('email')) {
                    $query->where('email', 'like', "%{$request->get('email')}%");
                }
            })
            ->make(true);
        }

        return view('users::users',[
            'data' => $data
        ]);
    }
}
