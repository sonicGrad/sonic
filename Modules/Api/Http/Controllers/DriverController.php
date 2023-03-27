<?php

namespace Modules\Api\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DriverController extends Controller{
    private $database;

    public function __construct(){
        $this->middleware('auth:api', [
            'except' => []
        ]);
        $this->database = \App\Firebase\FirebaseService::connect();

    }

    public function homePage(){
        $user = auth()->guard('api')->user();
        $driver = \Modules\Drivers\Entities\Driver::withoutGlobalScope('App\Scopes\ActiveScope')
        ->where('user_id', $user->id)
        ->first();
        if(!$driver){
            return response()->json([
                'message' => 'Not Allowed'
            ],403);
        }

       $deliverOrder = \Modules\Products\Entities\DriverOrderState::where('driver_id',$driver->id )
       ->where('status_id', 11)
       ->whereDate('created_at',  Carbon::today())
       ->latest()->count();
       $inProgress = \Modules\Products\Entities\DriverOrderState::where('driver_id',$driver->id )
       ->whereNotIn('status_id', [11,14])
       ->whereDate('created_at',  Carbon::today())
       ->latest()->count();

       $returnOrders = \Modules\Products\Entities\DriverOrderState::where('driver_id',$driver->id )
       ->where('status_id', 14)
     
       ->whereDate('created_at',  Carbon::today())
       ->latest()->count();
       $ordersList = \Modules\Products\Entities\Orders::with('user','last_status.state','vendor')
        ->withoutGlobalScope('App\Scopes\ActiveVendorScope')
        ->where('driver_id' , $driver->id)
        ->latest()
        ->first();

        
        $orderListLast = null;
        if($ordersList){
            $lastStateForDriver = \Modules\Products\Entities\DriverOrderState::with('order_status')
            ->where('driver_id', $driver->id)
            ->where('order_id', $ordersList->id)
            ->latest()
            ->first();
            $orderListLast = [
                'id' => $ordersList->id,
                'user' => $ordersList->user->first_name,
                'image_url' => $ordersList->vendor->vendor_logo_url,
                'mobile_no' => $ordersList->user->mobile_no,
                'location' => json_decode($ordersList->location),
                'last_state' => $lastStateForDriver->order_status->getTranslation('name', \App::getLocale()),
                'date' => Carbon::parse($lastStateForDriver->created_at)->format('Y-m-d') 
           ];
        }

        $ordersConfirm = \Modules\Drivers\Entities\DriverOrdersBuffering::with('order.vendor')->where('driver_id', $driver->id)->latest()->first();
        $ordersConfirmLast = null;
        if($ordersConfirm){
            $ordersConfirmLast = [ 
                'id' => $ordersConfirm->order->id,
                'image_url' => $ordersConfirm->order->vendor->vendor_logo_url,
                'location' => json_decode($ordersConfirm->order->location),
                'location_formate' => getLocationFromLatAndLong( json_decode($ordersConfirm->order->location)->lat ?? 34.620745, json_decode($ordersConfirm->order->location)->long ?? 34.620745, app()->getLocale()),
                'delivery_date' => Carbon::parse($ordersConfirm->created_at)->format('d M Y') ,      
                'delivery_time' => Carbon::parse($ordersConfirm->created_at)->format('h:i:s a')   
            ];
        }
        return response()->json([
            'data'=> [
                'deliver_order' => $deliverOrder,
                'return_orders' => $returnOrders,
                'in_progress' =>1,
                'orders_confirm' => $ordersConfirmLast,
                'orders_list' => $orderListLast
            ]
            ]);
    }
    public function orderList(Request $request){
        $user = auth()->guard('api')->user();
        $driver = \Modules\Drivers\Entities\Driver::withoutGlobalScope('App\Scopes\ActiveScope')
        ->where('user_id', $user->id)
        ->first();
        if(!$driver){
            return response()->json([
                'message' => 'Not Allowed'
            ],403);
        }
        $orders = \Modules\Products\Entities\Orders::with('user','vendor')
        ->withoutGlobalScope('App\Scopes\ActiveVendorScope')
        ->where('driver_id' , $driver->id)
        ->paginate($request->page_size ?? 10);
        $orderList = collect([]);
        foreach ($orders as $order) {
            $lastStateForDriver = \Modules\Products\Entities\DriverOrderState::with('order_status')
            ->where('driver_id', $driver->id)
            ->where('order_id', $order->id)
            ->latest()
            ->first();
            $orderList->push([
                'id' => $order->id,
                'user' => $order->user->first_name,
                'image_url' => $order->vendor->vendor_logo_url,
                'mobile_no' => $order->user->mobile_no,
                'location' => json_decode($order->location),
                'last_state' => $lastStateForDriver->order_status->getTranslation('name', \App::getLocale()),
                'date' =>Carbon::parse($lastStateForDriver->created_at)->format('Y-m-d')
                //  $lastStateForDriver->created_at
            ]);
        }

        return response()->json([
            'data' => $orderList
        ]);
    }
    public function orderDetails(Request $request){
        $request->validate([
            'order_id' => 'required'
        ]);
        $user = auth()->guard('api')->user();
        $driver = \Modules\Drivers\Entities\Driver::withoutGlobalScope('App\Scopes\ActiveScope')->where('user_id', $user->id)->first();
        if(!$driver){
            return response()->json([
                'message' => 'Not Allowed'
            ],403);
        }
        $order = \Modules\Products\Entities\Orders::
        withTrashed()
        ->with('user','vendor.user')
        ->withoutGlobalScope('App\Scopes\OrderDetailsActiveProductsScope')
        ->with(['order_details.product' => function ($query) {
            $query->withoutGlobalScope('App\Scopes\ActiveStateForProducts')
            ->withoutGlobalScope('App\Scopes\AdminActiveScope')
            ->withoutGlobalScope('App\Scopes\ActiveScope');
        }])

        ->with(['order_details' => function ($query) {
            $query->withoutGlobalScope('App\Scopes\OrderDetailsActiveProductsScope');
        }])
       

        ->where('driver_id' , $driver->id)
        ->whereId( $request->order_id)
        ->first();
        if(!$order){
            return response()->json([
                'message' => 'Make order'
            ],403);
        }
       $product_list = collect([]);
        if($order->order_details->count() == 0){
            return response()->json([
                'message' => 'empty'
            ],403);
        }
        foreach ($order->order_details as $products) {
            $product = $products->product;
            $product_list->push([
                'name' => $product->getTranslation('name', \App::getLocale()),
                'description' =>$product->getTranslation('description', \App::getLocale()),
                'price' => $products->price,
                'quantity' => $products->quantity,
            ]);
        }
        $lastStateForDriver = \Modules\Products\Entities\DriverOrderState::with('order_status')
            ->where('driver_id', $driver->id)
            ->where('order_id', $order->id)
            ->latest()
            ->first();
        $oderStates = \Modules\Products\Entities\DriverOrderState::with('order_status')
        ->where('driver_id', $driver->id)
        ->where('order_id', $order->id)
        ->get();
        $orderStateList = collect([]);
        foreach($oderStates as $orderState){
            $orderStateList->push([
                'id'=> $orderState->id,
                'name'=> $orderState->order_status->getTranslation('name', \App::getLocale()),
                'time'=> $orderState->time,
            ]);
        }
        return response()->json([
                'data' => [
                    'id' => $order->id,
                    'image_url' => $order->vendor->vendor_logo_url,
                    'date' => Carbon::parse( $lastStateForDriver->created_at)->format('Y-m-d'),
                    'user_name' => $order->user->first_name,
                    'total' => $order->total,
                    'userDetails' => [
                        'user_name' => $order->user->first_name,
                        'mobile_no' => $order->user->mobile_no,
                        'location' => json_decode($order->location),
                        'location_formate' => getLocationFromLatAndLong( json_decode($order->location)->lat ?? 34.620745, json_decode($order->location)->long ?? 34.620745, app()->getLocale()),
                        'last_state' => $lastStateForDriver->order_status->getTranslation('name', \App::getLocale()),
                    ],
                    'last_state' => $lastStateForDriver->order_status->getTranslation('name', \App::getLocale()),
                    'sellerDetails' =>[
                        'id' => $order->vendor->id,
                        'company_name' => $order->vendor->company_name,
                        'email' => $order->vendor->user->email,
                        'mobil_no' => $order->vendor->user->mobil_no,
                        'location' => json_decode($order->vendor->location),
                        'location_formate' => getLocationFromLatAndLong( json_decode($order->vendor->location)->lat ?? 34.620745, json_decode($order->vendor->location)->long ?? 34.620745, app()->getLocale()),
                        
                    ],
                    'orderDetails' => $product_list,
                    'orderState' => $orderStateList
                ]
            ]);

    }
    public function orderConfirm(Request $request){
       $user = auth()->guard('api')->user();
       $driver = \Modules\Drivers\Entities\Driver::with('order_state')->withoutGlobalScope('App\Scopes\ActiveScope')
       ->where('user_id', $user->id)
       ->first();
       if(!$driver){
           return response()->json([
               'message' => 'Not Allowed'
            ],403);
        }
       
        $ordersConfirms = \Modules\Drivers\Entities\DriverOrdersBuffering::where('driver_id', $driver->id)
        ->with('order.vendor')
        ->orderby('created_at', 'DESC')
        ->paginate($request->page_size ?? 10);
        $ordersConfirmsList = collect([]);
        foreach ($ordersConfirms as $ordersConfirm) {
            $ordersConfirm->order;
            $ordersConfirmsList->push([
                'id' => $ordersConfirm->order->id,
                'image_url' => $ordersConfirm->order->vendor->vendor_logo_url,
                'location' => json_decode($ordersConfirm->order->location),
                'location_formate' => getLocationFromLatAndLong( json_decode($ordersConfirm->order->location)->lat ?? 34.620745, json_decode($ordersConfirm->order->location)->long ?? 34.620745, app()->getLocale()),
                'delivery_date' => Carbon::parse($ordersConfirm->created_at)->format('d M Y') ,      
                'delivery_time' => Carbon::parse($ordersConfirm->created_at)->format('h:i:s a')       
            ]);
        }
        return $ordersConfirmsList;
    }
    public function ListOFStatus(){
        $user = auth()->guard('api')->user();
        $driver = \Modules\Drivers\Entities\Driver::withoutGlobalScope('App\Scopes\ActiveScope')
        ->where('user_id', $user->id)
        ->first();
        if(!$driver){
            return response()->json([
                'message' => 'Not Allowed'
            ],403);
        }
       $orderStatuesList = collect([]);
        $orderStatues = \Modules\Products\Entities\OrderStatus::where('type','1')->get();
        foreach ($orderStatues as $orderStatue) {
            $orderStatuesList->push([
                'id'=> $orderStatue->id,
                'name'=> $orderStatue->getTranslation('name', \App::getLocale()),
            ]);
        }
        return response()->json([
            'data' => $orderStatuesList
        ]);
    }
    public function addNewState(Request $request){
    
        $request->validate([
            'order_id' => 'required',
            'status_id' => 'required',
        ]);
        
        \DB::beginTransaction();
        try {
            $user = auth()->guard('api')->user();
            $driver = \Modules\Drivers\Entities\Driver::withoutGlobalScope('App\Scopes\ActiveScope')
            ->where('user_id', $user->id)->first();
            if(!$driver){
                return response()->json([
                    'message' => __('Not Allowed')
                ],403);
            }
            
            $order = \Modules\Products\Entities\Orders::
            withoutGlobalScope('App\Scopes\ActiveVendorScope')
            ->where('driver_id' , $driver->id)
            ->whereId( $request->order_id)
            ->first();
            if(!$order){
                return response()->json([
                    'message' => 'Not Allowed'
                ],403);
            }
           
            if(\Modules\Products\Entities\DriverOrderState::
            where('driver_id', $driver->id)
            ->where('status_id', $request->status_id )
            ->where('order_id', $request->order_id )
            ->first()
            ){
                return response()->json([
                    'message' => 'Not Allowed This Status is Already Exsist'
                ],403);
            }
            if(!$request->status_id == '6' && !$request->status_id == '9'){
                return response()->json([
                    'message' => 'Not Allowed'
                ],403);
            }
            $driverORderStates =new \Modules\Products\Entities\DriverOrderState;
            $driverORderStates->driver_id  = $driver->id; 
            $driverORderStates->status_id  = $request->status_id; 
            $driverORderStates->order_id  = $request->order_id; 
            $driverORderStates->time  = date('H:i:s');
            $driverORderStates->save();
            $orderState =new \Modules\Products\Entities\OrderState;
            $orderState->order_id = $request->order_id;
            $orderState->driver_id = $driver->id;
            $orderState->status_id = $request->status_id;
            $orderState->save();
            $order->last_status = $orderState->id;
            $order->save();
            if($request->status_id == 11){
                $driver->status_id = 1;
                $driver->save();
            }
            $this->database->getReference('orders/' .$request->order_id)
            ->update([
                'status_id' => $request->status_id,
            ]);
        \DB::commit();
        } catch (\Exception $e) {

        \DB::rollback();
        return response()->json(['message' => $e->getMessage()], 403);
        }
        return response()->json([
            'data' =>  $driverORderStates
        ]);
    }
    public function driverProfile(){
        $user = auth()->guard('api')->user();
        $driver = \Modules\Drivers\Entities\Driver::with('user','type_of_driver')->withoutGlobalScope('App\Scopes\ActiveScope')
        ->where('user_id', $user->id)->first();
        if(!$driver){
            return response()->json([
                'message' => 'Not Allowed'
            ],403);
        }
        $number_of_order = \Modules\Products\Entities\Orders::
        withoutGlobalScope('App\Scopes\ActiveVendorScope')
        ->where('driver_id' , $driver->id)
        ->whereHas('last_status' ,function($q){
            $q->where('status_id', '8');
        })
        ->count();
        return response()->json([
            'data' => [
                'location' => json_decode($driver->location),
                'location_formate' => getLocationFromLatAndLong( json_decode($driver->location)->lat ?? 34.620745, json_decode($driver->location)->long ?? 34.620745, app()->getLocale()),
                'name' => $driver->user->full_name,
                'percentage_of_rating' => $driver->user->percentage_of_rating,
                'mobile_no' => $driver->user->mobile_no,
                'email' => $driver->user->email,
                'number_of_order' => $number_of_order,
                'image' => $driver->person_image_url,
                'type_of_driver' => $driver->type_of_driver->getTranslation('name', \App::getLocale()),
            ]
        ]);
    }
    public function addImage(Request $request ){
        $user =auth()->guard('api')->user();
        $driver = \Modules\Drivers\Entities\Driver::withoutGlobalScope('App\Scopes\ActiveScope')
        ->where('user_id', $user->id)->first();
        
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $extension = strtolower($request->file('file')->extension());
            $media_new_name = strtolower(md5(time())) . "." . $extension;
            $collection = "driver-image";

            $driver->addMediaFromRequest('file')
                ->usingFileName($media_new_name)
                ->usingName($request->file('file')->getClientOriginalName())
                ->toMediaCollection($collection);
                $driver->save();
                return response()->json([
                    'data' => $driver->person_image_url
                ]);
        }
    }
    public function updateMobileNumber(Request $request){
        $request->validate([
            'mobile_no' => 'required'
        ]);
        $user =auth()->guard('api')->user();
        $mobile_no = \Modules\Users\Entities\User::where('id', '<>', $user->id)
        ->where('mobile_no', $request->mobile_no)
        ->first();
        if($mobile_no){
            return response()->json(['message' =>'This Mobile Are Already Exisit'],403);
        }
        $user->mobile_no = $request->mobile_no;
        $user->save();
        return response()->json([
            'data' => $user
        ]);
    }
    public function updateLocation(Request $request){
        $request->validate([
            'location' => 'required'
        ]);
        $user =auth()->guard('api')->user();
        $driver = \Modules\Drivers\Entities\Driver::withoutGlobalScope('App\Scopes\ActiveScope')
        ->where('user_id', $user->id)->first();
        $driver->location = $request->location;
        $driver->save();
        $number_of_order = \Modules\Products\Entities\Orders::
        withoutGlobalScope('App\Scopes\ActiveVendorScope')
        ->where('driver_id' , $driver->id)
        ->whereHas('last_status' ,function($q){
            $q->where('status_id', '8');
        })
        ->count();
        $this->database->getReference('drivers/' .$driver->id)
            ->update([
                'status_id' => $request->status_id == 1 ? 'active' : 'deactivate',
                'location' => $request->location
            ]);
        
        return response()->json([
            'data' => [
                'location' => json_decode($driver->location),
                'location_formate' => getLocationFromLatAndLong( json_decode($driver->location)->lat ?? 34.620745, json_decode($driver->location)->long ?? 34.620745, app()->getLocale()),
                'name' => $driver->user->full_name,
                'percentage_of_rating' => $driver->user->percentage_of_rating,
                'mobile_no' => $driver->user->mobile_no,
                'email' => $driver->user->email,
                'number_of_order' => $number_of_order,
                'image' => $driver->person_image_url,
                'type_of_driver' => $driver->type_of_driver->getTranslation('name', \App::getLocale()),
            ]
        ]);
    }
    public function addFeedbackForDriver(Request $request){
        $request->validate([
            'order_id'=>'required',
            'rating'=>'required|min:0|max:5',
            'driver_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $user =auth()->guard('api')->user();
            $driver = \Modules\Drivers\Entities\Driver::withoutGlobalScope('App\Scopes\ActiveScope')->where('id', $request->driver_id)->first();
            if(!$driver){
                return response()->json(['message'=> 'Not Allow'],403);
            }
            $order = \Modules\Products\Entities\Orders::whereId($request->order_id)
            ->where('driver_id', $driver->id)
            ->where('buyer_id',  $user->id)
            ->first();
            if(!$order){
                return response()->json(['message'=> 'Not Allow'],403);
            }
            $rating = \Modules\Users\Entities\Rate::where('order_id', $order->id)->first();
            if($rating){
                return response()->json(['message'=> 'You Make Rate Before In Same Order'],403);
            }
            $rate = new \Modules\Users\Entities\Rate;
            $rate->user_id = $user->id;
            $rate->order_id = $order->id;
            $rate->rateable_id = $driver->id;
            $rate->rateable_type =  'Modules\Drivers\Entities\Driver';
            $rate->rating = $request->rating;
            $rate->feedback = $request->feedback;
            $rate->save();
            $userDriver = \Modules\Users\Entities\User::whereId($driver->user_id)->first();
            $userDriver->number_of_raters = $userDriver->number_of_raters + 1;
            $userDriver->percentage_of_rating = 
                ($rate->rating +  $userDriver->percentage_of_rating) / $userDriver->number_of_raters;
            $userDriver->save();
            \DB::commit();
        } catch (\Exception $e) {

        \DB::rollback();
        return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'Ok']);
    }
    
}
