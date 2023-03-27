<?php

namespace Modules\Api\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    use \App\Traits\NearestDriver;
    use \App\Traits\NearestVendors;
    private $database;
    public function __construct()
    {
        $this->middleware('auth:api', [
            'except' => []
        ]);
        $this->database = \App\Firebase\FirebaseService::connect();
    }
    public function FoundNewDriver(Request $request)
    {
        $request->validate([
            'order_id' => 'required'
        ]);
        \DB::beginTransaction();
        try {
            $black_list = \Modules\Products\Entities\OrderState::where('order_id', $request->order_id)
                // ->whereHas('driver', function($q){
                //     $q->where('status_id', '2');
                // })
                ->where('status_id', '5')
                ->get('driver_id');

            $black_list = $black_list->toArray();
            $order = \Modules\Products\Entities\Orders::with('user')
                ->where('checkout_status', 1)
                ->whereId($request->order_id)
                ->first();
            $orderLocation = json_decode($order->location);
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $order->vendor->id)->first();
            if (!$vendor) {
                return response()->json(['message' => 'Vendor is deactivate']);
            }


            $vendorLocation = json_decode($vendor->location);
            $distance = $this->distanceBetweenTwoPoints($vendorLocation->lat, $vendorLocation->long, $orderLocation->lat, $orderLocation->long);
            // find nearest driver without blackList(drivers make reject to spesfic order)
            $driver =  $this->NearestDriverByType($vendorLocation->lat, $vendorLocation->long, $black_list);

            $orderState = new \Modules\Products\Entities\OrderState;
            $orderState->order_id = $order->id;
            $orderState->driver_id = $driver->id;
            $orderState->status_id = '3';
            $orderState->save();
            $driverOrdersBuffering = new \Modules\Drivers\Entities\DriverOrdersBuffering;
            $driverOrdersBuffering->order_id = $order->id;
            $driverOrdersBuffering->driver_id = $driver->id;
            $driverOrdersBuffering->save();

            $driverORderStates = new \Modules\Products\Entities\DriverOrderState;
            $driverORderStates->order_id  = $order->id;
            $driverORderStates->driver_id  = $driver->id;
            $driverORderStates->status_id  = '3';
            $driverORderStates->time  = date('H:i:s');
            $driverORderStates->save();
            $order->order_driver_reach_time = Carbon::now();
            $order->save();
            // dd($driverOrdersBuffering);
            $this->database->getReference('orders/' . $request->order_id)
                ->update([
                    'buffering_driver_id' => $driver->id,
                    'status_id' => '3'
                ]);
            $this->database->getReference('drivers/' . $driver->id)
                ->update([
                    'buffer_orders' => ['order_id' => $order->id]
                ]);
            $user = \Modules\Users\Entities\User::whereId($driver->user_id)->first();
            $user->notify(new \Modules\Drivers\Notifications\NotifyDriverOfNewOrder($order));
            $vendor->notify(new \Modules\Vendors\Notifications\NotifyVendorOfNewOrder($order));

            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'Order was checked']);
    }

    public static function dispatchOrderToNewDriver(Request $request)
    {
        $orders = \Modules\Products\Entities\Orders::where('last_status', null)
            ->where('checkout_status', 1)
            ->where('order_driver_reach_time', '<=', Carbon::now()->subMinute()->format('Y-m-d H:i:s'))
            ->get();
        $orderControllerClass = new OrdersController();
        foreach ($orders as $order) {
            $request->merge(['order_id' => $order->id]);
            $orderControllerClass->changeDriverAutoBySystem($request);
        }
        return response()->json(['ok']);
    }
    public  function changeDriverAutoBySystem(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $bufferOrder = \Modules\Drivers\Entities\DriverOrdersBuffering::where('order_id', $request->order_id)
                ->latest()
                ->first();
            if (!$bufferOrder) {
                $this->FoundNewDriver($request);
            } else {
                $driver = \Modules\Drivers\Entities\Driver::whereId($bufferOrder->driver_id)->First();

                $orderState = \Modules\Products\Entities\OrderState::where('driver_id', $driver->id)
                    ->where('order_id', $request->order_id)
                    ->latest()
                    ->first();

                if (!$orderState || $orderState->status_id != '3') {
                    return response()->json([
                        'message' => 'Not Allowed This Order Not For This Driver'
                    ], 403);
                }

                $order = \Modules\Products\Entities\Orders::whereId($request->order_id)->first();
                if ($order->last_status != null) {
                    return response()->json([
                        'message' => 'This Order is have status '
                    ], 403);
                }

                $driverOrdersBuffering = \Modules\Drivers\Entities\DriverOrdersBuffering::where('driver_id', $driver->id)
                    ->where('order_id', $request->order_id)
                    ->first();
                if (!$driverOrdersBuffering) {
                    return response()->json([
                        'message' => 'Not Allowed This Order Not For You'
                    ], 403);
                }
                $driverOrdersBuffering->delete();

                $vendor = \Modules\Vendors\Entities\Vendors::whereId($order->seller_id)->active()->first();
                // $vendor = \Modules\Users\Entities\User::whereId($vendor->user_id)->first();
                // $vendor->notify(new \Modules\Drivers\Notifications\NotifyDriverOfNewOrder($order));

                $this->changeStatus($request, $driver);

                $order->last_status  = null;
                $order->save();
                $buffering = \Modules\Drivers\Entities\DriverOrdersBuffering::where('order_id',  $request->order_id)
                    ->where('driver_id', $driver->id)
                    ->delete();

                $this->FoundNewDriver($request);
            }
            \DB::commit();


            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['data' => 'ok']);
    }
    public function checkout(Request $request)
    {

        $user = auth()->guard('api')->user();
        \DB::beginTransaction();
        try {
            $order = \Modules\Products\Entities\Orders::with('user')->where('buyer_id', $user->id)
                ->where('checkout_status', null)
                ->first();
            if (!$order) {
                return response()->json([
                    'message' => 'Make new order'
                ], 405);
            }

            if (count($order->order_details) == 0) {
                return response()->json(['you should add to cart first'], 403);
            }
            $black_list = \Modules\Products\Entities\OrderState::where('status_id', '5')
                ->where('order_id', $order->id)
                ->get(['driver_id']);
            $black_list = $black_list->toArray();
            $orderLocation = json_decode($order->location);
            $vendor = \Modules\Vendors\Entities\Vendors::where('id', $order->vendor->id)->active()->first();
            if (!$vendor) {
                return response()->json([
                    'message' => 'This vendor is  deactivate'
                ], 403);
            }

            $vendorLocation = json_decode($vendor->location);
            $distance = $this->distanceBetweenTwoPoints($vendorLocation->lat, $vendorLocation->long, $orderLocation->lat, $orderLocation->long);

            $driver =  $this->NearestDriverByType($orderLocation->lat, $orderLocation->long, $black_list);
            if (!$driver) {
                return response()->json([
                    'message' => "Sorry no driver found, you can wait a few minutes if you don't find one too, please contact technical support "
                ], 403);
            }
            $orderState = new \Modules\Products\Entities\OrderState;
            $orderState->order_id = $order->id;
            $orderState->driver_id = $driver->id;
            $orderState->status_id = '3';
            $orderState->save();

            $driverState = new  \Modules\Products\Entities\DriverOrderState();
            $driverState->order_id  = $order->id;
            $driverState->driver_id = $driver->id;
            $driverState->status_id = '3';
            $driverState->time = date('H:i:s');
            $driverState->save();

            $driverOrdersBuffering = new \Modules\Drivers\Entities\DriverOrdersBuffering;
            $driverOrdersBuffering->order_id = $order->id;
            $driverOrdersBuffering->driver_id = $driver->id;
            $driverOrdersBuffering->save();

            $user = \Modules\Users\Entities\User::whereId($driver->user_id)->first();
            $order->checkout_status = 1;
            $order->order_driver_reach_time = now();
            $order->save();
            $this->database->getReference('orders/' . $request->order_id)
                ->update([
                    'status_id' => '3',
                    'checkout' => true,
                    'buffering_driver_id' => $driver->id,
                ]);

            $user->notify(new \Modules\Drivers\Notifications\NotifyDriverOfNewOrder($order));
            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'Wait For Driver Accept Order'], 200);
    }


    public function DriverAcceptORRejectOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'flag' => 'required'
        ]);
        \DB::beginTransaction();
        try {
            $user = auth()->guard('api')->user();

            $driver = \Modules\Drivers\Entities\Driver::where('user_id', $user->id)->first();
            if (!$driver) {
                return response()->json([
                    'message' => 'Not Allowed'
                ], 403);
            }
            $orderState = \Modules\Products\Entities\OrderState::where('driver_id', $driver->id)
                ->where('order_id', $request->order_id)
                ->latest()
                ->first();

            if (!$orderState || $orderState->status_id != '3') {
                return response()->json([
                    'message' => 'Not Allowed This Order Not For You'
                ], 403);
            }

            $order = \Modules\Products\Entities\Orders::with('vendor')->whereId($request->order_id)->first();
            if ($order->last_status != null) {
                return response()->json([
                    'message' => 'This Order is have status '
                ], 403);
            }

            $driverOrdersBuffering = \Modules\Drivers\Entities\DriverOrdersBuffering::where('driver_id', $driver->id)
                ->where('order_id', $request->order_id)
                ->first();
            if (!$driverOrdersBuffering) {
                return response()->json([
                    'message' => 'Not Allowed This Order Not For You'
                ], 403);
            }
            $driverOrdersBuffering->delete();

            $vendor = \Modules\Vendors\Entities\Vendors::whereId($order->seller_id)->active()->first();
            // $vendor = \Modules\Users\Entities\User::whereId($vendor->user_id)->first();
            // $vendor->notify(new \Modules\Drivers\Notifications\NotifyDriverOfNewOrder($order));
            if ($request->flag == '1') {
                $orderState_id = $this->changeStatus($request, $driver);
                // make order for driver
                $order->last_status  = $orderState_id;
                $order->driver_id  = $driver->id;
                $order->save();
                // make driver deactivate
                $driver->status_id  = '2';
                $driver->save();
                $this->database->getReference('orders/' . $request->order_id)
                    ->update([
                        'driver_id' => $driver->id,
                    ]);
                $this->database->getReference('drivers/' . $driver->id)
                    ->set([
                        'status_id' => $driver->status_id == 1 ? 'active' : 'deactivate',
                        'vendor_location' => $order->vendor->location,
                        'distance_location' => $order->location,
                        'current_order' => $order->id,
                    ]);
                $this->removeBufferAndFindDriver($request, $driver);
            }

            if ($request->flag == '2') {
                if (!$request->reason) {
                    return response()->json([
                        'message' => 'Please Add Reason For Reject This Order'
                    ], 403);
                }

                $reason = new \Modules\Users\Entities\Reason;
                $reason->order_id = $order->id;
                $reason->reasonable_id = $driver->id;
                $reason->reasonable_type = 'Modules\Drivers\Entities\Driver';
                $reason->reason = $request->reason;
                $reason->save();
                $this->changeStatus($request, $driver);


                $order->last_status  = null;
                $order->save();
                $buffering = \Modules\Drivers\Entities\DriverOrdersBuffering::where('order_id',  $request->order_id)
                    ->where('driver_id', $driver->id)
                    ->delete();

                $this->FoundNewDriver($request);
                \DB::commit();
            }

            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['data' => $orderState]);
    }
    public function removeBufferAndFindDriver($request, $driver)
    {
        $orders_buffering = \Modules\Drivers\Entities\DriverOrdersBuffering::where('order_id', '<>', $request->order_id)
            ->where('driver_id', $driver->id)
            ->get();
        $buffering = \Modules\Drivers\Entities\DriverOrdersBuffering::where('order_id',  $request->order_id)
            ->where('driver_id', $driver->id)
            ->delete();
        foreach ($orders_buffering  as $buffer) {
            $orderState = new \Modules\Products\Entities\OrderState;
            $orderState->order_id = $buffer->order_id;
            $orderState->driver_id = $buffer->driver_id;
            $orderState->status_id = '5';
            $orderState->save();
            $buffer->delete();
            $request->merge([
                'order_id' => $buffer->order_id
            ]);
            $this->FoundNewDriver($request);
        }
    }
    public function changeStatus($request, $driver)
    {
        $orderState = new \Modules\Products\Entities\OrderState;
        $orderState->order_id = $request->order_id;
        $orderState->driver_id = $driver->id;
        $orderState->status_id = $request->flag == '1' ? 4 : 5;
        $orderState->save();
        $driverORderStates = new \Modules\Products\Entities\DriverOrderState;
        $driverORderStates->order_id  = $request->order_id;
        $driverORderStates->driver_id  = $driver->id;
        $driverORderStates->status_id  = $orderState->status_id;
        $driverORderStates->time  = date('H:i:s');
        $driverORderStates->save();

        return $orderState->id;
    }

    public function OrderStates(Request  $request)
    {
        $request->validate([
            'order_id' => 'required'
        ]);
        $user = auth()->guard('api')->user();
        $order = \Modules\Products\Entities\Orders::with('offer.offer.type', 'coupon.coupon', 'order_details.variation.attributes')
            ->whereId($request->order_id)->where('buyer_id', $user->id)->first();
        if (!$order) {
            return response()->json([
                'message' => 'Not Allowed'
            ], 403);
        }
        $orderStates = \Modules\Products\Entities\OrderState::with('state')
            ->where('order_id', $request->order_id)
            ->orderBy('id', 'DESC')
            ->get();

        $orderStates_list = collect([]);
        foreach ($orderStates as $orderState) {
            $item = [];
            if ($orderState->status_id == 4) {
                $item = [
                    'order_id' => $orderState->order_id,
                    'status_id' => $orderState->status_id,
                    'status_name' => "Your Request has begin Approved",
                    'created_at' => Carbon::parse($orderState->created_at)->format('h:i:s a'),
                ];
            }
            if ($orderState->status_id == 7) {
                $item = [
                    'order_id' => $orderState->order_id,
                    'status_id' => $orderState->status_id,
                    'status_name' => "Yor Request is begin possessed",
                    'created_at' => Carbon::parse($orderState->created_at)->format('h:i:s a'),
                ];
            }
            if ($orderState->status_id == 10) {
                $item = [
                    'order_id' => $orderState->order_id,
                    'status_id' => $orderState->status_id,
                    'status_name' => "In Progress",
                    'created_at' => Carbon::parse($orderState->created_at)->format('h:i:s a'),
                ];
            }
            if ($orderState->status_id == 11) {
                $item = [
                    'order_id' => $orderState->order_id,
                    'status_id' => $orderState->status_id,
                    'status_name' => "Delivered",
                    'created_at' => Carbon::parse($orderState->created_at)->format('h:i:s a'),
                ];
            }

            count($item) > 0  ? $orderStates_list->push($item) : '';
        }
        $orderDetail = [
            'id' => $order->id,
            'taxAmount' => 5,
            'delivery_price' => 5,
            'total' => $order->total,
            'after_discount' => $order->after_discount,
            'coupon_discount' => $order->coupon ? $order->coupon->value : null,
            'offer_discount' => $order->offer ? $order->offer->offer->value : null,
        ];
        $products = collect([]);
        foreach ($order->order_details as $product) {
            $productVariations = collect([]);
            foreach ($product->variation->attributes as $productVariation) {
                // return $productVariation->type;
                $productVariations->push([
                    'id' => $productVariation->id,
                    'variation_id' => $productVariation->variation_id,
                    'attribute_id' => $productVariation->type_id,
                    'attribute_name' => $productVariation->type->getTranslation('name', \App::getLocale()),
                    'value' => $productVariation->value,
                ]);
            }
            $products->push([
                'id' => $product->product->id,
                'name' => $product->product->getTranslation('name', \App::getLocale()),
                'productVariation' => $productVariations,
            ]);
        }
        if (!$order->products) {
            return response()->json(['you should add to cart first'], 403);
        }

        return response()->json([
            'data' => [
                'orderStatusList'  => $orderStates_list,
                'products' => $products,
                'orderDetail' => $orderDetail
            ],
        ]);
    }
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'code' => 'required',
            'vendor_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $user = auth()->guard('api')->user();
            $coupon = \Modules\Vendors\Entities\Coupon::where('vendor_id', $request->vendor_id)
                ->where('code', $request->code)->first();
            if (!$coupon) {
                return response()->json([
                    'message' => 'Not Found Coupon'
                ], 403);
            }
            $order = \Modules\Products\Entities\Orders::whereId($request->order_id)
                ->where('buyer_id', $user->id)
                ->where('seller_id', $request->vendor_id)
                ->first();
            if (!$order) {
                return response()->json([
                    'message' => 'Not Allowed'
                ], 403);
            }
            $orderUser = \Modules\Users\Entities\UserCoupon::where('order_id', $request->order_id)->first();
            if ($orderUser) {
                return response()->json([
                    'message' => 'Not Allowed You Beneficiary of a coupon on the same order '
                ], 403);
            }
            $offerUser = \Modules\Users\Entities\UserOffer::where('order_id', $request->order_id)->where('user_id', $user->id)->first();
            if ($offerUser) {
                return response()->json([
                    'message' => 'Not Allowed You Beneficiary of a coupon on the same order '
                ], 403);
            }
            if ($order->seller_id != $coupon->vendor_id) {
                return response()->json([
                    'message' => 'Not Allowed'
                ], 403);
            }
            if ($order->total < $coupon->amount) {
                return response()->json([
                    'message' => 'Not Allowed Your total must be over: ' . $coupon->amount
                ], 403);
            }
            $orderUser = new \Modules\Users\Entities\UserCoupon;
            $orderUser->user_id  = $user->id;
            $orderUser->order_id   = $request->order_id;
            $orderUser->coupon_id   = $coupon->id;
            $orderUser->value   = $coupon->value;
            $orderUser->save();
            $order_total = $order->total;
            $order->after_discount = $order_total - ($orderUser->value);
            $order->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'Your Coupon Was Applied']);
    }
    public function applyOffer(Request $request)
    {
        $request->validate([
            'offer_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $user = auth()->guard('api')->user();

            $offer = \Modules\Vendors\Entities\Offer::where('id', $request->offer_id)->first();
            if (!$offer) {
                return response()->json([
                    'message' => 'Not Found Offer'
                ], 403);
            }
            $order = \Modules\Products\Entities\Orders::where('buyer_id', $user->id)
                ->where('checkout_status', null)
                ->first();
            if (!$order) {
                return response()->json([
                    'message' => 'Not Allowed'
                ], 403);
            }
            $orderUser = \Modules\Users\Entities\UserCoupon::where('order_id', $request->order_id)->first();
            if ($orderUser) {
                return response()->json([
                    'message' => 'Not Allowed You Beneficiary of a coupon and offer on the same order '
                ], 403);
            }
            $offerUser = \Modules\Users\Entities\UserOffer::where('order_id', $request->order_id)->where('user_id', $user->id)->first();
            if ($offerUser) {
                return response()->json([
                    'message' => 'Not Allowed You Beneficiary of a another offer on the same order '
                ], 403);
            }
            if ($order->seller_id != $offer->vendor_id) {
                return response()->json([
                    'message' => 'Not Allowed'
                ], 403);
            }


            if ($order->amount !== 0) {
                if ($order->total < $offer->amount) {
                    return response()->json([
                        'message' => 'Not Allowed Your total must be over: ' . $offer->amount
                    ], 403);
                }
            }
            $productsNotInOffer = array_diff($order->order_details()->pluck('product_id')->toArray(), $offer->offer_product()->pluck('product_id')->toArray());
            if (count($productsNotInOffer) > 0) {
                $message = 'Sorry You have products not in offer you can just beneficiary just on products in offer ' . " \n "  .
                    'The products out of our offer it ' .  " \n ";
                foreach (array_unique($productsNotInOffer) as $product) {
                    $productOut = \Modules\Products\Entities\Product::whereId($product)->first();
                    $message .= $productOut->getTranslation('name', \App::getLocale()) . " \n ";
                }
                return response()->json([
                    'message' => $message
                ], 403);
            }
            $orderUser = new \Modules\Users\Entities\UserOffer();
            $orderUser->user_id  = $user->id;
            $orderUser->order_id   = $request->order_id;
            $orderUser->offer_id   = $offer->id;
            $orderUser->save();
            $order_total = $order->total;
            $offer->value ? $order->after_discount = $order_total - ($order_total * $orderUser->value) : null;
            $order->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'Your Offer Was Applied']);
    }

    public function offersForUser(Request $request)
    {
        $request->validate([
            'order_id' => 'required'
        ]);
        $user = auth()->guard('api')->user();
        $order = \Modules\Products\Entities\Orders::whereId($request->order_id)->where('buyer_id', $user->id)->first();
        if (!$order) {
            return response()->json([
                'message' => 'Not Allowed'
            ], 403);
        }
        $offers = \Modules\Vendors\Entities\Offer::where('vendor_id', $order->seller_id)->get();
        $ordersProductsIds = $order->order_details()->pluck('product_id')->toArray();
        $data = collect([]);
        foreach ($offers as $offer) {
            if ($offer->amount) {
                if ($order->total >= $offer->amount) {
                    $data->push([
                        'message' =>  'You can benefit from freeShipping ',
                        'offer' => new \Modules\Vendors\Transformers\OfferResource($offer)
                    ]);
                } else {
                    $data->push([
                        'message' =>  'You no satisfy the target total to benefit for offer',
                        'offer' => new \Modules\Vendors\Transformers\OfferResource($offer)
                    ]);
                }
            }

            if ($offer->amount == null) {
                $productsNotInOffer = collect([]);
                $offerProductsIds = $offer->offer_product()->pluck('product_id')->toArray();
                foreach ($ordersProductsIds as $product_id) {
                    if (!in_array($product_id, $offerProductsIds)) {
                        $product = \Modules\Products\Entities\Product::whereId($product_id)->first();
                        $productsNotInOffer->push([
                            'id' => $product->id,
                            'name' => $product->name
                        ]);
                    }
                }
                if (count($productsNotInOffer) > 0) {
                    $data->push([
                        'message' =>  'You have Products not in Offer You can\'t use offer',
                        'offer' => new \Modules\Vendors\Transformers\OfferResource($offer),
                        'productsNotInOffer' => $productsNotInOffer
                    ]);
                } else {
                    $data->push([
                        'message' =>  'You have OFfer In Your Product ',
                        'offer' => new \Modules\Vendors\Transformers\OfferResource($offer),
                    ]);
                }
            }
        }
        return response()->json([
            'data' => $data
        ]);
    }

    public function checkOrder()
    {
        $user = auth()->guard('api')->user();
        $order = \Modules\Products\Entities\Orders::where('buyer_id', $user->id)->where('checkout_status', null)->first();
        return response()->json(['data' =>  $order ? $order->id : null], $order ? 200 : 403);
    }
}
