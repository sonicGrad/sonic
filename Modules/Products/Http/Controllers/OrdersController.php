<?php

namespace Modules\Products\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class OrdersController extends Controller{
    public function __construct(){
        $this->middleware(['auth']);
    }
    public function manage(Request $request){
        \Auth::user()->authorize('products_module_orders_manage');

        $data['activePage'] = ['orders' => 'orders'];
        $data['breadcrumb'] = [
            ['title' => 'Orders '],
        ];
        if ($request->ajax()) {
            $user= \Auth::user();
            if($user->hasRole('vendor')){
                // $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
                $vendorsParent = \Modules\Vendors\Entities\Vendors::with('children')->where('user_id', $user->id)->first();
                $vendor_ids = $vendorsParent->children()->pluck('id');
                $vendor_ids->push($vendorsParent->id);
                $data = \Modules\Products\Entities\Orders::
                with('order_details.product','user','vendor','last_status.state','order_details.variation.attributes','driver.user','offer.offer.type','coupon.coupon','add_status.state')
                ->whereIn('seller_id', $vendor_ids)
                ->select('*');
            }else{
                $data = \Modules\Products\Entities\Orders::with('order_details.product','order_details.variation.attributes','user','vendor','add_status.state','last_status.state','driver.user','offer.offer.type','coupon.coupon')->select('*');
            }
            return Datatables::of($data)
            ->addColumn('action', function ($order) {
                $btn = '';
                $user= \Auth::user();
                    $btn .= '<button type="button" data-action="changeState" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#changeStatus' . $order->id . '">';
                    $btn .= "add new status";
                    $btn .= '</button>';
                    $btn .= '<div class="modal fade"  id="changeStatus'. $order->id. '" tabindex="-1" role="dialog" aria-labelledby="changeStatus'. $order->id.'" aria-hidden="true">';
                    $btn .= '  <div class="modal-dialog" role="document">';
                    $btn .= '<div class="modal-content" >';
                    $btn .= '<div class="modal-header">';
                    $btn .= '<h5 class="modal-title" id="">Add Status For Order</h5>';
                    $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                    $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                    $btn .= '<div class="modal-body" style="height: 100px;">';
                    $btn .= '<div class="form-group">';
                    $btn .= '<label  class="col-sm-2 control-label" for="" >Admin Status</label>';
                    $btn .= '<div class="col-sm-10">';
                    $btn .= '<select name="admin_status" id="admin_status data-action="change" class="form-control ">';
                    $btn .= '<option value="">Choose Status...</option>';
                    foreach (\Modules\Products\Entities\OrderStatus::where('type', '2')->get() as $admin_status){
    
                        $btn .= '<option data-id="'. $order->id. '" value="';
                       $btn .= $admin_status->id;
                       $btn .= '"';
                       if($admin_status->id   == $order->admin_status){
                           $btn  .= 'selected';
                       }
                       $btn .= '>';
                       $btn .= $admin_status->name ;
                       $btn .= '</option>' ;
                       
                    }
                       $btn .= '</select>';
                    $btn .= '<p class="invalid-feedback"></p>';
                    $btn .= '</div>';
                    $btn .= ' </div>';
                
            
       
                    $btn .= '</div>';
                    $btn .= ' <div class="modal-footer"><button type="button" data-id="'. $order->id. '" data-action="save-new-status" class="btn btn-primary">Save changes</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                    $btn .= '</div></div></div></div>';
                return $btn;
            })
            
            ->rawColumns(['action'])
            ->filter(function ($query) use ($request) {
                if ($request->has('vendor_id') && $request->get('vendor_id') != null) {
                    $query->where('seller_id', 'like', "%{$request->get('vendor_id')}%");
                }
            })
            ->toJson();
        }

        return view('products::orders',[
            'data' => $data,
            'vendors' => \Modules\Vendors\Entities\Vendors::get()
        ]);
    }
    public function changeStatusForAdmin(Request $request, $id){
        // \Auth::user()->authorize('core_module_ads_change_status');
        
        $request->validate([
            'status' => 'required'
        ]);
        $order =  \Modules\Products\Entities\Orders::whereId($id)->first();
        $vendor = \Modules\Vendors\Entities\Vendors::whereId($order->seller_id)->first();
        $orderStatus = new \Modules\Products\Entities\OrderState();
        $orderStatus->order_id  = $id ;
        $orderStatus->vendor_id    = $vendor->id ;
        $orderStatus->status_id    = $request->status;
        $orderStatus->created_by    = auth()->user()->id;
        $orderStatus->save();
        return response()->json(['message' => 'ok'], 200);

    }
}
