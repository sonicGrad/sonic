<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardController extends Controller{
    public function __construct(){
        $this->middleware(['auth']);
    }

    public function manage(){
        // \Auth::user()->authorize('core_module_dashboard_manage');

        $data['activePage'] = ['dashboard' => 'dashboard'];
        $data['breadcrumb'] = [
            ['title' => 'Welcome To Dashboard'],
        ];
        $contact_us = \Modules\CMS\Entities\ContactUs::orderBy('id', 'DESC')->get();
        return view('dashboard' , [
            'data' => $data,
            'contact_us' => $contact_us,
            'numberOFUsers' => \Modules\Users\Entities\User::count(),
            'numberOFVendors' => \Modules\Vendors\Entities\Vendors::count(),
            'numberOFOrder' => \Modules\Products\Entities\Orders::where('last_status', '<>', null)->count(),
            'vendorsLocations' => \Modules\Vendors\Entities\Vendors::pluck('location'),
            'driversLocations' => \Modules\Drivers\Entities\Driver::pluck('location'),
        ]);
    }
}
