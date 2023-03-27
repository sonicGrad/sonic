<?php

namespace Modules\Api\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VendorAppController extends Controller{
    public function __construct(){
        $this->middleware('auth:api', [
        'except' => [
            'typeOfVendor'
        ]
     ]);
    }
    public function checkVendor(){
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }
        return $vendor;
    }
    public function typeOfVendor(){
        $types = collect([]);
        $typeOFVendors = \Modules\Vendors\Entities\TypeOFVendor::get();
        foreach($typeOFVendors as $type){
            $types->push([
                'id' => $type->id,
                'name'=>  $type->getTranslation('name', \App::getLocale()),
            ]);
        }
        return response()->json([
            'data' => $types
        ]);
    }

    public function addLogoForVendor(Request $request){
        $request->validate([
            'vendor_id' => 'required'
        ]);
        $vendor = \Modules\Vendors\Entities\Vendors::whereId($request->vendor_id)->first();
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $extension = strtolower($request->file('file')->extension());
            $media_new_name = strtolower(md5(time())) . "." . $extension;
            $collection = "vendor_logo_url";
            
            $vendor->addMediaFromRequest('file')
                ->usingFileName($media_new_name)
                ->usingName($request->file('file')->getClientOriginalName())
                ->toMediaCollection($collection);
            return response()->json(['message' => 'ok']);
        }
    }

    public function addBranch(Request $request){
        $request->validate([
            'location' => 'required',
            'mobile_no' => 'required',
            'email' => 'required',
        ]);
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id',$user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'This vendor not exist'],403 );
        }
        $mobile_no = \Modules\Users\Entities\User::where('mobile_no', $request->mobile_no)->first();
        if($mobile_no){
            return response()->json(['message' => 'This mobile number is already exsist'],403 );
        }
        $email = \Modules\Users\Entities\User::where('email', $request->email)->first();
        if($email){
            return response()->json(['message' => 'This mobile number is already exsist'],403 );
        }
        $brach = new \Modules\Vendors\Entities\Vendors;
        $brach->parent_id = $vendor->id;
        $brach->mobile_no = $request->mobile_no;
        $brach->email = $request->email;
        $brach->location = $request->location;
        $brach->save();
        return response()->json(['message' => 'ok', 'data'=> $brach]);
    }

    public function listOfOrders(){
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }
        $inprogress =collect([]);
        $delivered =collect([]);
        $orders = \Modules\Products\Entities\Orders::with('add_status', 'user')->where('seller_id', $vendor->id)->get();
        // return $orders;
        foreach($orders as $order){
            $orderStatus = $order->add_status()->with('state')->where('vendor_id', $vendor->id)->latest()->first();
            // return $order;
            if(isset( $orderStatus)){
                // return $orderStatus;
                $orderDetails = [
                    
                    'id' => $order->id,
                    'location' => json_decode($order->location),
                    'location_formate' => getLocationFromLatAndLong( json_decode($order->location)->lat ?? 34.620745, json_decode($order->location)->long ?? 34.620745, app()->getLocale()),
                    'date' => Carbon::parse($order->created_at)->format('h:i a'),
                    'total' => $order->total,
                    'taxAmount' =>1200,
                    'deliveryAmount'=> 10,
                    'paymentStatus'=> 'approve',
                    'paymentMethod' => 'online',
                    'orderStatus' => $orderStatus->state->getTranslation('name', \App::getLocale()),
                    'user_info' => [
                        'id' => $order->user->id,
                        'name' => $order->user->first_name,
                    ],
                ];
                if($orderStatus->status_id == 7){
                    $inprogress->push($orderDetails);
                }else if($orderStatus->status_id == 10){
                    $delivered->push($orderDetails);
                }
            }

        }
        return response()->json([
            'data'=>[
                'inprogress' => $inprogress
                ,'delivered'=> $delivered
            ]
        ]);

        // $inProgressOrder->add_status()->where('vendor_id', $vendor->id)->latest()->first();
    }

    public function orderDetails($id){
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }
        $order = \Modules\Products\Entities\Orders::
        with('order_details.variation.attributes.type','add_status', 
        'user','order_details.product', 'order_details.variation.attributes','offer.offer.type','coupon.coupon')
        ->where('seller_id', $vendor->id)
        ->first();
        if(!$order){
            return response()->json(['message' => 'Not allow'],403 );
        }
        $orderStatus = $order->add_status()->with('state')->where('vendor_id', $vendor->id)->get();
        $orderStatusList = collect([]);
        foreach($orderStatus as $orderState){
            $orderStatusList->push([
                'id' => $orderState->id,
                'created_at' => $orderState->created_at,
                'name' => $orderState->state->getTranslation('name', \App::getLocale()),
            ]);
        }
        $products = collect([]);
        foreach($order->order_details as $product){
            $productVariations = collect([]);
            foreach($product->variation->attributes as $productVariation){
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
                'productVariation' => $productVariations
            ]);
           
        }
         $orderDetails = [
            'id' => $order->id,
            'location' => json_decode($order->location),
            'location_formate' => getLocationFromLatAndLong( json_decode($order->location)->lat ?? 34.620745, json_decode($order->location)->long ?? 34.620745, app()->getLocale()),
            'date' => Carbon::parse($order->created_at)->format('h:i a'),
            'total' => $order->total,
            'after_discount' => $order->after_discount,
            'coupon_discount' => $order->coupon ? $order->coupon->value : null,
            'offer_discount' => $order->offer ? $order->offer->offer->value : null,
            'taxAmount' =>1200,
            'user_info' => [
                'id' => $order->user->id,
                'name' => $order->user->first_name,
                'email' => $order->user->email,
            ],
            'products' => $products,
            'orderStatusList' => $orderStatusList
        ];
        return response()->json([
            'data' => $orderDetails
        ]);
    }
    
    public function products(Request $request){
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }
        $request->validate([
            'flag' => 'required'
        ]);

        if($request->flag == 1){
            $products = \Modules\Products\Entities\Product::with('status')->withoutGlobalScope('App\Scopes\ActiveScope')
            ->where('vendor_id', $vendor->id)
            ->get();
        }else if($request->flag == 2){
            $products = \Modules\Products\Entities\Product::with('status')->withoutGlobalScope('App\Scopes\ActiveScope')
            ->where('status_id', 1)
            ->where('vendor_id', $vendor->id)
            ->get();
        }else{
            $products = \Modules\Products\Entities\Product::with('status')->withoutGlobalScope('App\Scopes\ActiveScope')
            ->where('status_id', 2)
            ->where('vendor_id', $vendor->id)
            ->get();
        }
        $productsList = collect([]);
        foreach($products as $product){
            // return $products;
            $numberOFOrders = \Modules\Products\Entities\OrderDetails::where('product_id', $product->id)->count();
            $productsList->push([
                'id' => $product->id,
                'name' => $product->getTranslation('name', \App::getLocale()),  
                'description' => $product->getTranslation('description', \App::getLocale()),  
                'price' => $product->price,
                'quantity' => $product->quantity,
                'image_url' => $product->image_url,
                'percentage_of_rating' => $product->percentage_of_rating,
                'number_of_raters' => $product->number_of_raters,
                'numberOFOrders' => $numberOFOrders,
                'status' => $product->status? $product->status->getTranslation('name', \App::getLocale()) : '', 
            ]);
        }
        return response()->json([
            'data' => $productsList 
        ]);
    }

    public function storeProduct(Request $request){
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }

        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'product_code' => 'required',
            'category_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $user = auth()->guard('api')->user();
            $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
            if(!$vendor){
                return response()->json(['message' =>'Not Allowed'],403); 
            }
            $branch = null;
            // if($user->hasRole('vendor') && $user->hasRole('main branch supplier')){
            //     if(!$request->branch_id){
            //         return response()->json(['message' =>'Not Allowed You Should Add Branch'],403); 
            //     }
            //     $branch = \Modules\Vendors\Entities\Vendors::where('id',$request->branch_id)->first();
            //     if(!$branch){
            //         $branch = \Modules\Vendors\Entities\Vendors::
            //         where('id',$request->branch_id)
            //         ->where('parent_id', $vendor->id)
            //         ->first();
                    
            //         return response()->json([$vendor->id],403); 
            //         if(!$branch){
            //             return response()->json(['message' =>'Not Allowed'],403); 
            //         }
            //     }

            // }
            $product = \Modules\Products\Entities\Product::where('vendor_id' , $vendor->id)
            ->where('product_code', $request->product_cod)->first();
            if($product){
                return response()->json(['message' =>'This Product Are Already Exisit'],403);
            }
            $product = new \Modules\Products\Entities\Product;
            $product
            ->setTranslation('name', 'en', $request->name_en)
            ->setTranslation('name', 'ar',  $request->name_ar);

            $product
            ->setTranslation('description', 'en', $request->description_en)
            ->setTranslation('description', 'ar',  $request->description_ar);
            $product->category_id = $request->category_id;
            $product->vendor_id  = $vendor->id;
            $product->product_code = $request->product_code;
            $product->status_id = '1' ;
            $user->hasRole('trust_vendor') ? $product->admin_status  = '1' : $product->admin_status  = '2';
            $product->created_by = \Auth::user()->id;
            $product->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok', 'data'=> $product->id]);

    }
  
    public function addImageForProduct(Request $request){
        $request->validate([
            'product_id' => 'required'
        ]);
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }
        $id = $user->id;
        $product = \Modules\Products\Entities\Product::
        withoutGlobalScope('App\Scopes\ActiveScope')
        ->withoutGlobalScope('App\Scopes\ActiveStateForProducts')
        ->withoutGlobalScope('App\Scopes\AdminActiveScope')
        ->whereId($request->product_id)
        ->first();
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $extension = strtolower($request->file('file')->extension());
            $media_new_name = strtolower(md5(time())) . "." . $extension;
            $collection = "product-image";
            
            $product->addMediaFromRequest('file')
                ->usingFileName($media_new_name)
                ->usingName($request->file('file')->getClientOriginalName())
                ->toMediaCollection($collection);
            return response()->json(['message' => 'ok']);
        }
    }
    public function addAttributesToProduct(Request $request){
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }
        $request->validate([
            'product_id' => 'required',
            'price' => 'required'
        ]);
        // return $request['attributes'][0];
        \DB::beginTransaction();
        try {
            if($request['attributes']){
                foreach ($request['attributes'] as  $attribute) {
                    $product = \Modules\Products\Entities\Product::whereId($request->product_id)->first();
                    if(isset($attribute['variation_id'])){
                        $productVariation = \Modules\Products\Entities\ProductVariation::whereId($attribute['variation_id'])->first();
                        if(!$productVariation){
                            return response()->json(['message'=> 'Not Allow'],403);
                        }
                        
                        $productVariation->price = $attribute['price'];
                        $productVariation->quantity = $attribute['quantity'];
                        $productVariation->save();
                    
                    }else{

                        if($attribute['value1'] != null || $attribute['value2'] != null ){
                           
                            $productVariation =new \Modules\Products\Entities\ProductVariation();
                            $productVariation->product_id = $request->product_id;
                            $productVariation->created_by  = \Auth::user()->id;
                            $productVariation->save();

                            $variationAttribute =new \Modules\Products\Entities\VariationAttribute;
                            $variationAttribute->variation_id = $productVariation->id;
                            if($attribute['value1'] != null){
                                $variationAttribute =new \Modules\Products\Entities\VariationAttribute;
                                $variationAttribute->variation_id = $productVariation->id;
                                $variationAttribute->type_id = $attribute['attibute1'];

                                $variationAttribute->value = $attribute['value1'];
                                $variationAttribute->save();

                            }
                            if($attribute['value2'] != null){
                                $variationAttribute =new \Modules\Products\Entities\VariationAttribute;
                                $variationAttribute->variation_id = $productVariation->id;
                                $variationAttribute->type_id = $attribute['attibute2'];
                                $variationAttribute->value = $attribute['value2'];
                                $variationAttribute->save();

                            }
                            $productVariation->price = $attribute['price'];
                            $productVariation->quantity = $attribute['quantity'];
                            $productVariation->save();
                        }
                    }
            }
           }
            $DefaultAttribute = \Modules\Products\Entities\VariationAttribute::with('variation')->where('type_id', '1')
            ->whereHas('variation.product', function($q) use($request){
                $q->where('id', $request->product_id);
            })
            ->first();

            if(!$DefaultAttribute){
                $productVariation =new \Modules\Products\Entities\ProductVariation();
                $productVariation->product_id = $request->product_id;
                $productVariation->created_by  = \Auth::user()->id;
                $productVariation->save();
                $variationAttribute =new \Modules\Products\Entities\VariationAttribute;

            }else{
                $productVariation = \Modules\Products\Entities\ProductVariation::whereId($DefaultAttribute->variation_id)->first();
                $variationAttribute = \Modules\Products\Entities\VariationAttribute::whereId($DefaultAttribute->id)->first();

            }
            $variationAttribute->variation_id = $productVariation->id;
            $variationAttribute->type_id = '1'; 
            $productVariation->price = $request->price;
            $productVariation->quantity =$request->quantity;
            $productVariation->save();
            $variationAttribute->save();

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }
        return response()->json(['message' => 'ok']);


    }

    public function couponStore(Request $request){
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }
        $request->validate([
            'code' => 'required',
            'name_ar' => 'required',
            'name_en' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'starting_data' => 'required',
            'ended_data' => 'required',
            'type_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $offerAvilable = \Modules\Vendors\Entities\Offer::where('vendor_id', $vendor->id)->first();
            if ($offerAvilable) {
                return response()->json(['message' => 'You Can\'t Add  Coupon You Have Avilable Offers'],403); 
            }
           
            $coupon = \Modules\Vendors\Entities\Coupon::where('code', $request->code)
            ->where('vendor_id', $vendor->id)
            ->first();
            if($coupon){
                return response()->json([
                    'message' => 'This Code Is Already Exisit'
                ],403);
            }
            $coupon = new \Modules\Vendors\Entities\Coupon;
            $coupon->code = $request->code;
            $coupon
            ->setTranslation('name', 'en',  $request->name_en)
            ->setTranslation('name', 'ar',   $request->name_ar);
            $coupon
            ->setTranslation('description', 'en',  $request->description_en)
            ->setTranslation('description', 'ar',   $request->description_ar);
            $coupon->vendor_id = $vendor->id;
            $coupon->amount = $request->amount;
            $coupon->starting_data = $request->starting_data;
            $coupon->ended_data = $request->ended_data;
            $coupon->type_id  = $request->type_id ;
            $coupon->status = '1';
            $user->hasRole('trust_vendor') ? $coupon->admin_status  = '1' : $coupon->admin_status  = '2';
            $coupon->created_by  =$user->id;

            $coupon->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
    }
    public function ratingPersonage(){
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }
        $count = \Modules\Users\Entities\Rate::where('rateable_id',$vendor->id )
        ->where('rateable_type','Modules\Vendors\Entities\Vendors')->count();
         $ratings = \Modules\Users\Entities\Rate::where('rateable_id',$vendor->id )
        ->where('rateable_type','Modules\Vendors\Entities\Vendors')
        ->select('rating', \DB::raw('count(id) as count'))
        ->groupBy('rating')
        ->get();
        $ratingList =collect([]);
        foreach ($ratings as $rating) {
            $ratingList->push([
                'rating' => $rating->rating,
                'percentage' =>intval($rating->count / $count *100),
            ]);
        }
        return response()->json(['data' => $ratingList]);
        
    }
    public function ratingPage(){
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }
        $ratings = \Modules\Users\Entities\Rate::with('product','user')
        ->where('rateable_id',$vendor->id )
        ->where('rateable_type','Modules\Vendors\Entities\Vendors')->get();
        $ratingList =collect([]);
        foreach($ratings as $rating){
            $ratingList->push([
                'id' => $rating->id,
                'rating' => $rating->rating,
                'date' => Carbon::parse($rating->created_at)->format('h:i a'),
                'feedback' => $rating->feedback,
                'product' => [
                    'id' => $rating->product->id,
                    'name' => $rating->product->getTranslation('name', \App::getLocale()),
                    'image_url' => $rating->product->image_url,
                    
                ],
                'user'=>[
                    'id' => $rating->user->id,
                    'name' => $rating->user->first_name,
                    'location_formate' =>  getLocationFromLatAndLong( json_decode($rating->user->location)->lat ?? 34.620745, json_decode($rating->user->location)->long ?? 34.620745, app()->getLocale()),
                ]

            ]);
        }
        return $ratingList;

    }
    public function offerStore(Request $request){
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'starting_data' => 'required',
            'ended_data' => 'required',
            'type_id' => 'required',
            // 'products_id' => 'required',
        ]);

        \DB::beginTransaction();
        try {
           
            // $coupon = \Modules\Vendors\Entities\Coupon::where('vendor_id', $vendor->id)->first();
            // if($coupon){
            //     return response()->json(['message' => 'You can\'t Add Offer You have Avilable Coupons'],403);
            // }
            if($request->products_id ){

                foreach ($request->products_id as $id) {
                    $product = \Modules\Products\Entities\Product::where('vendor_id', $vendor->id)->whereId($id)->first(); 
                    if(!$product){
                        return $id;
                        return response()->json(['message' => 'Not Allow'],403); 
                    }
                }
                $offerAvilable = \Modules\Vendors\Entities\OfferProduct::whereHas('offer', function($q)use($request, $vendor){
                    $q->where('vn_offers.vendor_id', $vendor->id);
                    $q->whereIn('vn_offers_products.product_id', $request->products_id);
                })
                ->get();
                if(count($offerAvilable) > 0){
                    return response()->json(['message' => 'You Can\'t Add  Product Exsist In Another Order'],403); 
                }
            }
            if($request->type_id == '1' && $request->value == null){
                return response()->json(['message' => 'That Should Add Percentage of discount']);
            }
            if($request->type_id == '3' && $request->amount == null){
                return response()->json(['message' => 'That Should Add Amount To Free Shipping Offer']);
            }
            $offer = new \Modules\Vendors\Entities\Offer;
            $offer
            ->setTranslation('name', 'en',  $request->name_en)
            ->setTranslation('name', 'ar',   $request->name_ar);
            $offer
            ->setTranslation('description', 'en',  $request->description_en)
            ->setTranslation('description', 'ar',   $request->description_ar);
            $offer->starting_data = $request->starting_data;
            $offer->ended_data = $request->ended_data;
            $offer->vendor_id = $vendor->id;
            $offer->status = '1';
            $user->hasRole('trust_vendor') ? $offer->admin_status  = '1' : $offer->admin_status  = '2';
            $offer->amount = $request->amount;
            $offer->value = $request->value;
            $offer->type_id = $request->type_id;
            $offer->created_by  =$user->id;

            $offer->save();
            if($request->products_id){
                foreach($request->products_id as $product_id){
                    $offer_products = new \Modules\Vendors\Entities\OfferProduct;
                    $offer_products->product_id = $product_id;
                    $offer_products->offer_id = $offer->id;
                    $offer_products->save();
                }
            }
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok','data' => $offer->id]);
    } 
    public function addImageForOffer(Request $request){
        $request->validate([
            'offer_id' => 'required'
        ]);
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }
        $ad = \Modules\Vendors\Entities\Offer::whereId($request->offer_id)->where('vendor_id', $vendor->id)->first();
        if(!$ad){
            return response()->json(['message' => 'Not allow'],403 );
        }
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $extension = strtolower($request->file('file')->extension());
            $media_new_name = strtolower(md5(time())) . "." . $extension;
            $collection = "offer-image";

            $ad->addMediaFromRequest('file')
                ->usingFileName($media_new_name)
                ->usingName($request->file('file')->getClientOriginalName())
                ->toMediaCollection($collection);
            return response()->json(['message' => 'ok']);
        }
    }

    public function profile(){
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }
        $productsCount = \Modules\Products\Entities\Product::where('vendor_id', $vendor->id)->count();
        $ordersCount = \Modules\Products\Entities\Orders::where('seller_id', $vendor->id)->where('checkout_status', '<>', null)->count();
        $rating = \Modules\Users\Entities\Rate::where('rateable_id',$vendor->id )
        ->where('rateable_type','Modules\Vendors\Entities\Vendors')->get(['rating']);
        $percentageOfRating =intval($rating->sum('rating') / $rating->count()) ;
        return response()->json([
            'data' => [
                'company_name' => $vendor->company_name,
                'email' => $vendor->user->email,
                'mobile_no' => $vendor->user->mobile_no,
                'rating' => $percentageOfRating,
                'products_count' => $productsCount,
                'orders_count' => $ordersCount,
                'balance' => 720.50,
                'about_store' => $vendor->description
            ]
        ]);
        
    }

    public function updateProfile(Request $request){
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }
        if($request->brach_id){
            $vendor = \Modules\Vendors\Entities\Vendors::where('parent_id', $vendor->id)->whereId($request->brach_id)->first();
        }
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }
        $request->validate([
            'location' => 'required',
            'mobile_no' => 'required',
            'email' => 'required',
            'company_name' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $vendor->company_name = $request->company_name;
            $vendor->location = $request->location;
            $user->mobile_no = $request->mobile_no;
            $user->email = $request->email;
            // $vendor->website = $request->website;
            $vendor->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }
        return response()->json(['message' => 'ok','data' => $vendor->id]);

    }

    public function branches(){
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }
        $vendors = $vendor->children()->with('user')->get();
        $vendorsList = collect([]);
        foreach($vendors as $vendor){
            $vendorsList->push([
                'id' => $vendor->id,
                'company_name' => $vendor->company_name,
                'email' => $vendor->user->email,
                'mobile_no' => $vendor->user->mobile_no,
                'location' => json_decode($vendor->location),
                'parent_id' => $vendor->parent_id
            ]);
        }
        return response()->json(['data' => $vendorsList]);

    }

    public function homePage(){
        $user =auth()->guard('api')->user();
        $vendor = \Modules\Vendors\Entities\Vendors::where('user_id', $user->id)->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'],403 );
        }

        $productsCount = \Modules\Products\Entities\Product::where('vendor_id', $vendor->id)->count();
        $availableProducts =  \Modules\Products\Entities\Product::where('vendor_id', $vendor->id)->where('status_id', '1')->count();
        $deactivateProducts =  \Modules\Products\Entities\Product::where('vendor_id', $vendor->id)->where('status_id', '2')->count();
        
        $newOrders = \Modules\Products\Entities\Orders::where('last_status', '13')
        ->whereDate('created_at',  Carbon::today())
        ->where('seller_id', $vendor->id)->count();
        $totalOrders = \Modules\Products\Entities\Orders::
        whereDate('created_at',  Carbon::today())
        ->where('seller_id', $vendor->id)->count();
        $now = Carbon::now()->month;
        $totalAmountOfOrders = \Modules\Products\Entities\Orders::
        whereDate('created_at',  Carbon::today())
        ->where('seller_id', $vendor->id)->sum('total');
        $orderWeekly = \DB::table('pm_products')
        ->selectRaw('week(created_at) as WEEKOFYEAR,month(created_at) as month, count(*) as total')
        ->whereRaw (\DB::raw("month(created_at)=$now"))
        ->groupBy(\DB::raw("week(created_at)"),\DB::raw("MONTH(created_at)"))
        ->get();

        // $orderMonthly = \DB::table('pm_products')
        // ->selectRaw('week(created_at) as WEEKOFYEAR,month(created_at) as month, count(*) as total')
        // ->whereRaw (\DB::raw("month(created_at)=$now"))
        // ->groupBy(\DB::raw("week(created_at)"),\DB::raw("MONTH(created_at)"))
        // ->get();
        $ordersMounthy = collect([]);
        $orderMonthlys = 0;
        for ($i=1; $i <= 12; $i++) { 
            $orderMonthlys = \Modules\Products\Entities\Product::
            whereMonth('created_at', $i)->count();
            $ordersMounthy->push(["$i" => $orderMonthlys  ]);
        }
        return $ordersMounthy;
        // select week(created_at) as WEEKOFYEAR,month(created_at) as month, count(*) as total 
        // from `pm_products`
        // where MONTH('created_at')=9 
        // group by week('created_at'), MONTH('created_at')

        // SELECT week(created_at) as WEEKOFYEAR,month(created_at) as month, count(*) as total
        // FROM users
        // WHERE month(created_at)=9
        // GROUP BY week(created_at),MONTH(created_at);
        return response()->json([
            'data' => [
                'products_count' => $productsCount,
                'available_products' => $availableProducts,
                'deactivate_products' => $deactivateProducts,
                'new_orders' => $newOrders,
                'total_orders' => $totalOrders,
                'total_amountO_of_orders' => $totalAmountOfOrders,
                'order_weekly' => $orderWeekly,
                'order_monthly' => $orderMonthly,
            ]
            ]);
    }
}
