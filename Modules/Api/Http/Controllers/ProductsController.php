<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
class ProductsController extends Controller{
    use \App\Traits\NearestVendors;
    public function __construct(){
        $this->middleware('auth:api', [
            'except' => ['vendorMenuCategory']
        ]);
    }

    public function homePage(Request $request){
        
        $user = auth()->guard('api')->user();
        $order = \Modules\Products\Entities\Orders::with('order_details.product')
        ->where('checkout_status', null)
        ->where('buyer_id', $user->id)
        ->first();
        if(!$order){
            return response()->json(['message' => 'Make order'],405);
        }
        $orderLocation =json_decode($order->location);
        $papularStores= $this->NearestVendorsByTypeWithFeature($orderLocation->lat, $orderLocation->long,'2','2');
        // return $papularStores;
        $papularStores_list = collect([]);
        foreach ($papularStores as $store) {
            $papularStores_list->push([
                'id' => $store->id,
                'name' =>  $store->company_name,
                'image_url' => $store->vendor_logo_url
            ]);
        }
        
        $categories = \Modules\Vendors\Entities\TypeOFVendor::get();
        $categories_list = collect([]);
        foreach ($categories as $category) {
            $categories_list->push([
                'id' => $category->id,
                'name' =>  $category->getTranslation('name', \App::getLocale()),
                'image_url' => $category->vendor_type_image_url
            ]);
        }
        $restaurants =  $this->NearestVendorsByType($orderLocation->lat, $orderLocation->long,'1',$user->province_id);
        $restaurants_list = collect([]);
        foreach ($restaurants as $restaurant) {
            $restaurants_list->push([
                'id' => $restaurant->id,
                'name' =>  $restaurant->company_name,
                'image_url' => $restaurant->vendor_logo_url,
                'location' => $restaurant->location,
                'open' => $restaurant->starting_time,
                'close' => $restaurant->closing_time,
                'address' => $restaurant->user->address,
                'province' => $restaurant->user->province->getTranslation('name', \App::getLocale()),
                'country' => $restaurant->user->province->country->getTranslation('name', \App::getLocale()),
                'rating' => $restaurant->user->percentage_of_rating,
                'distance' => $restaurant->distance,
                'time' => $restaurant->distance/1000/30*60 . 'to' . $restaurant->distance/1000/60*60
            ]);
        }
        $papularStars = $this->NearestVendorsByType($orderLocation->lat, $orderLocation->long,'3', $user->province_id);
        $papularStars_list = collect([]);
        foreach ($papularStars as $star) {
            $papularStars_list->push([
                'id' => $star->id,
                'name' =>  $star->company_name,
                'image_url' => $star->vendor_logo_url
            ]);
        }
        // dd('ddd');
        return response()->json([
            'data' => [
                'papular_stores' => $papularStores_list,
                'categories' => $categories_list,
                'offers_images' => \Modules\Vendors\Entities\Offer::getMedias(),
                'restaurant_near_you' => $restaurants_list,
                'papular_stars' => $papularStars_list
            ]
        ]);
    }

    public function spesficProduct(Request $request,$id){
        $user = auth()->guard('api')->user();

        $order = \Modules\Products\Entities\Orders::with('user')->where('buyer_id', $user->id)
        ->where('checkout_status', null)
        ->first();
        if(!$order){
            return response()->json([
                'message' => 'Make Order'
            ],405);
        }
        $orderLocation =json_decode($order->location);
        $product= \Modules\Products\Entities\Product::whereId($id)->first();
        $vendor = \Modules\Vendors\Entities\Vendors::whereId($product->vendor_id)->active()->first();
        $vendorLocation =json_decode($vendor->location);
        $distance =  $this->distanceBetweenTwoPoints($vendorLocation->lat, $vendorLocation->long,$orderLocation->lat, $orderLocation->long);
        $attributesList = collect([]);
        $attributes = \Modules\Products\Entities\VariationAttribute::with('type')->whereHas('variation.product', function($q)use($id){
            $q->where('id', $id);
        })
        ->distinct()
        ->get(['type_id']);
        foreach ($attributes as $attribute) {
            if($attribute->type_id != 1){
           
                $attribute_values = \Modules\Products\Entities\VariationAttribute::where('type_id',$attribute->type_id)
                ->whereHas('variation.product', function($q)use($product){
                    $q->where('id', $product->id);
                })
                ->distinct()
                ->pluck('value');
                $attributesList->push([
                    'attribute_id' =>  $attribute->type_id,
                    'attribute' => $attribute->type->getTranslation('name', \App::getLocale()),
                    'attribute_values' => $attribute_values[0] === null ? []:  $attribute_values
                ]);
            }
        }
        $medias = collect([]);
        foreach($product->images as $image){
            $medias->push([
                'image_url_' =>  url('/') . '/storage/app/public/' . $image->id . '/' . $image->file_name
            ]);
        }
        $variationAttribute =  \Modules\Products\Entities\VariationAttribute::with('variation')->where('type_id', '1')
        ->whereHas('variation.product', function($q)use($product){
            $q->where('id', $product->id);
        })->first ();
        return response()->json([
            'data' => [
                'id' => $id,
                'name' => $product->getTranslation('name', \App::getLocale()),
                'description' =>$product->getTranslation('description', \App::getLocale()),
                'price' => $product->price,
                'variation_id' => $variationAttribute->variation_id,
                'percentage_of_rating' => $product->percentage_of_rating,
                'media' => $medias,
                'distance' => $distance,
                'attributesList'=> $attributesList
            ]
        ]);
    }
    public function filterSearch(Request $request){
        // $request->validate([
        //   'order_id' => 'required'
        // ]);

        $user = auth()->guard('api')->user();
        $order = \Modules\Products\Entities\Orders::where('checkout_status' , null)->where('buyer_id', $user->id)->first();
        if(!$order){
            return response()->json([
                'message' => 'Make order'
            ],405);
        }
        if($request->location){
            $orderLocation = json_decode($request->location);
        }else{

            $orderLocation =json_decode($order->location);
        }
        $query = \Modules\Products\Entities\Product::with([]);
        if ($request->has('name') && $request->get('name') != null) {
          $query->where('name->ar', 'like', "%{$request->get('name')}%");
          $query->orWhere('name->en', 'like', "%{$request->get('name')}%");
        }
        if ($request->has('type_id') && $request->get('type_id') != null) {
            $query->whereHas('vendor.type_of_vendor', function($eloquent) use ($request){
                $eloquent->whereId(trim($request->get('type_id')));
            });
        }
        if ($request->has('vendor_id') && $request->get('vendor_id') != null) {
            $query->where('vendor_id', trim($request->get('vendor_id')));
        }
        if ($request->has('category_id') && $request->get('category_id') != null) {
            $query->where('category_id', trim($request->get('category_id')));
        }
        if ($request->has('budget') && $request->get('budget') != null) {
            $budget = explode(',',$request->budget);
            // return $budget;
            $variations = \Modules\Products\Entities\VariationAttribute::with('variation')->where('type_id', '1')
            ->whereHas('variation.product', function($q)use($budget){
                $q->whereBetween('price', $budget);
            })->get();
            $productsIds = collect([]);
           foreach($variations as $variation){
            $productsIds->push($variation->variation->product_id);         
          }
          $query->whereIn('id', $productsIds);
        }
        $products =  $query->get();
        $data = collect([]);
        foreach($products as $product){
        $vendorForDistance = \Modules\Vendors\Entities\Vendors::whereId($product->vendor_id)->active()->first();
        $vendorLocation =json_decode($vendorForDistance->location);
        $distance =  $this->distanceBetweenTwoPoints($vendorLocation->lat, $vendorLocation->long,$orderLocation->lat, $orderLocation->long);
        //   $distance =  $this->spesficVendorDistance($orderLocation->lat, $orderLocation->long,$product->vendor_id);
          $data->push([
            'id' => $product->id,
            'name' => $product->getTranslation('name', \App::getLocale()),
            'description' =>$product->getTranslation('description', \App::getLocale()),
            'price' => $product->price,
            'percentage_of_rating' => $product->percentage_of_rating,
            'image' => $product->image_url,
            'distance' => $distance,
            'location' => $vendorLocation,
            'location_formate' => getLocationFromLatAndLong( $vendorLocation->lat ?? 34.620745, $vendorLocation->long ?? 34.620745, app()->getLocale()),
            'isFavorite' => $product->is_favorite
          ]);
        }
        return response()->json([
          'data' => $data
        ]);
      }

    public function addAndDeleteFromCart(Request $request){
        $user = auth()->guard('api')->user();
        $request->validate([
            'flag' => 'required',
            'product_id' => 'required',
            'variation_id' => 'required',
        ]);
        $order = \Modules\Products\Entities\Orders::with('order_details.product')
        ->where('checkout_status', null)
        ->where('buyer_id', $user->id)
        ->first();
        if(!$order){
            return response()->json(['message' => 'Make order'],405);
        }
        $product = \Modules\Products\Entities\Product::whereId($request->product_id)
        ->whereHas('variations', function($q)use($request){
            $q->where('id', $request->variation_id);
        })
        ->first();
        if(!$product){
            return response()->json([
                'message' => 'This Product Not Found'
            ],403);
        }
        \DB::beginTransaction();
        try {
            if(!$order->seller_id){
                $order->seller_id = $product->vendor_id;
                $order->save();
            }
            if($order->seller_id != $product->vendor_id){
                if($order->order_details){
                    return response()->json([
                        'message' => 'This Order Not For Same Vendor'
                    ],403);
                }else{
                    $order->seller_id = $product->vendor_id;
                    $order->save();
                }
            }
            $product = \Modules\Products\Entities\ProductVariation::whereId($request->variation_id)->first();

            $order_details = \Modules\Products\Entities\OrderDetails::where('order_id', $order->id)
            ->where('variation_id', $request->variation_id)
            ->first();
            if(!$order_details && $request->flag == 2){
                return response()->json([
                    'message' => 'This Process Not Allowed'
                ],403);
            }
            if($order_details && $request->flag == 2 && $order_details->quantity == 1){
                $request->flag  = 3;
                // return response()->json([
                //     'message' => 'This Process Not Allowed You Can Delete Product From Cart'
                // ],403);
            }
            if(!$order_details && $request->flag == 1){
                $order_details = new \Modules\Products\Entities\OrderDetails;
                $order_details->order_id = $order->id;
                $order_details->product_id  = $request->product_id ;
                $order_details->variation_id  = $request->variation_id ;
                $order_details->price  =  $product->price ;
                $order_details->quantity  =  0 ;
                $order_details->save();
            }
            if($request->flag == '1'){
                if(!$request->quantity){
                    if($product->quantity === 0){
                        return response()->json([
                            'message' => 'This Process Not Allowed Not Avilable Quantity'
                        ],403);
                    }
                    //  $order_details->quantity = $order_details->quantity + 1;
                    $order_details->quantity = $order_details->quantity + 1;
                    $product->quantity = $product->quantity -1;
                    $product->save();
                }else{
                    if($product->quantity === 0){
                        return response()->json([
                            'message' => 'This Process Not Allowed Not Avilable Quantity'
                        ],403);
                    }
                    $order_details->quantity = $order_details->quantity + $request->quantity;

                }
                $order_details->save();
            }
            if($request->flag == 2){
                $order_details->quantity = $order_details->quantity - 1;
                $order_details->save();
                $product->quantity = $product->quantity +1;
                $product->save();
            }
            if($request->flag == 3){
               if(!$order_details){
                return response()->json([
                    'message' => 'This Product is already deleted'
                ],403);
               }
               $product->quantity = $product->quantity + $order_details->quantity;
               $product->save();
               $order_details->delete();
            }
            $order_details->total = $order_details->price * $order_details->quantity;
            $order_details->save();
            $order = \Modules\Products\Entities\Orders::with('order_details.product')
            ->where('checkout_status', null)
            ->where('buyer_id', $user->id)
            ->first();
            $order->total = $order->order_details->sum('total');
            if($order->coupon_for_user){
                $order->after_discount = $order->total  - $order->coupon_for_user->value;
            }
            $order->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }
        $order->save();
        return response()->json([
            'message' => 'This Process Succ'
            ,'total' => $order->total
        ]);
    }
    public function makeOrder(Request $request){
        $request->validate([
            'addressbook' => 'required'
        ]);
        $user = auth()->guard('api')->user();
       

        $order = \Modules\Products\Entities\Orders::where('buyer_id', $user->id)->where('checkout_status',  null)->first();
        if($order){
            return response()->json([
                'message' => 'Please Finish This Exist Order and make checkout before open new Order'
            ],403);
        }
        $address_book =  $user->address_book()->whereId($request->addressbook)->first();
        if(!$address_book){
            return response()->json([
                'message' => 'address book not found' 
            ],403);
        }
        // $order = \Modules\Products\Entities\Orders::where('buyer_id', $user->id)
        // ->where('seller_id', $request->vendor_id)->first();
        // if($order){
        //     return response()->json([
        //         'message' => 'This Order Already Exsist'
        //     ],403);
        // }
        if($request->vendor_id){
            $vendor = \Modules\Vendors\Entities\Vendors::whereId($request->vendor_id)->active()->first();
            if(!$vendor){
                return response()->json([
                    'message' => 'This Vendor Not Active'
                ],403);
            }
        }
        $order = new  \Modules\Products\Entities\Orders;
        // $order->seller_id = $request->vendor_id ;
        $order->buyer_id  = $user->id  ;
        $order->location  = $address_book->location  ;
        $order->save();
        return response()->json([
            'message' => 'Order Created Successfully',
            'data' => $order
        ]);


    }
    public function getProductsUnderOffer(Request $request, $id){
        $user = auth()->guard('api')->user();
        $offer = \Modules\Vendors\Entities\Offer::with('offer_product.product','type')->whereId($id)->first();
        $vendor = \Modules\Vendors\Entities\Vendors::with('user')->whereId($offer->vendor_id)->first();
       if(!$vendor){
        return response()->json([
            'message' => 'This Vendor no active'
        ],403);
        }
       $vendorLocation = json_decode($vendor->location);
       $order = \Modules\Products\Entities\Orders::where('buyer_id', $user->id)
       ->where('checkout_status',  null)
       ->first();
       $orderLocation = json_decode($order->location);
       $distance =  $this->distanceBetweenTwoPoints($vendorLocation->lat, $vendorLocation->long,$orderLocation->lat, $orderLocation->long);
       $productsList = collect([]);
        //    return $offer;
        $productsNotInOffer = count(array_diff($order->order_details()->pluck('product_id')->toArray(),$offer->offer_product()->pluck('product_id')->toArray())) >0 ? false : true;
       if($offer->offer_product){
            foreach($offer->offer_product as $product) {
                $spesficProduct = $product->product;
                $price_after_discount = null;
                if($offer->type_id == 1){
                    $price_after_discount = $spesficProduct->price * $offer->value;
                }else if($offer->type_id == 3){
                    $price_after_discount = null;
                    
                }
                $productsList->push([
                    'id' => $spesficProduct->id,
                    'name' => $spesficProduct->getTranslation('name',\App::getLocale()),
                    'image_url' => $spesficProduct->image_url,
                    'is_favorite' => $spesficProduct->is_favorite,
                    'percentage_of_rating' => $spesficProduct->percentage_of_rating,
                    'number_of_raters' => $spesficProduct->number_of_raters,
                    'price' => $spesficProduct->price,
                    'distance' => $distance,
                    // 'price_after_discount' => $price_after_discount,
                    // 'amount' => $spesficProduct->amount,
                    'province' => $vendor->user->province->getTranslation('name',\App::getLocale()),
                    'country' => $vendor->user->province->country->getTranslation('name',\App::getLocale()),
                    // 'type_id' => $offer->type->id,
                    // 'type_name' => $offer->type->getTranslation('name',\App::getLocale()),
                ]);
            }
       }
       
       return response()->json([
        'data' => $productsList
       ]);
    }
    public function getVendorsWithFreeShipping($id){
        
       $freeShippings = \Modules\Vendors\Entities\Offer::with('vendor','offer_product.product')->where('type_id', $id)->get();
       if(!$freeShippings){
        return response()->json(['message' => 'No Free Shipping Avilable Now']);
       }
       return $freeShippings;
       $product_list = collect([]);
      foreach ($freeShippings as $freeShipping) {
        foreach($freeShipping->offer_product as $product){
               $product_list->push([
                   'name' => $product->product->getTranslation('name', \App::getLocale()),
                   'description' =>$product->product->getTranslation('description', \App::getLocale()),
                   'price' => $product->product->price,
                   'percentage_of_rating' => $product->product->percentage_of_rating,
                   'media' => $product->product->image_url,
               ]);
    
           }
      }
       return response()->json([
        'data' => [
            'offer_info' => [
                'name' => $freeShipping->getTranslation('name', \App::getLocale()),
                'description' => $freeShipping->getTranslation('description', \App::getLocale()),
                'amount' => $freeShipping->amount,
                'value' => $freeShipping->value,
            ],
            'vendor' => [
                'id' => $freeShipping->vendor->id,
                'company_name' => $freeShipping->vendor->company_name,
                'vendor_logo_url' => $freeShipping->vendor->vendor_logo_url,
                'location' => json_decode($freeShipping->vendor->location),
            ],
            'products' => [
                $product_list
            ]
        ]
       ]) ;
    }
    public function favorites(Request $request){
        $request->validate([
            'order_id' => 'required'
        ]);
        $user = auth()->guard('api')->user();
        $order = \Modules\Products\Entities\Orders::whereId($request->order_id)->where('buyer_id', $user->id)->first();
        if(!$order){
            return response()->json([
                'message' => 'Make order'
            ],405);
        }
        
        $orderLocation =json_decode($order->location);
        $user = auth()->guard('api')->user();
       $favorites = \Modules\Users\Entities\Favorite::with('product')->where('user_id', $user->id)->get();
       $product_list_favorites =  collect([]);
       foreach ($favorites as $products) {
        $product = $products->product;
        $vendorForDistance = \Modules\Vendors\Entities\Vendors::whereId($product->vendor_id)->active()->first();
        $vendorLocation =json_decode($vendorForDistance->location);
        $distance =  $this->distanceBetweenTwoPoints($vendorLocation->lat, $vendorLocation->long,$orderLocation->lat, $orderLocation->long);
        $variationAttribute =  \Modules\Products\Entities\VariationAttribute::with('variation')->where('type_id', '1')
        ->whereHas('variation.product', function($q)use($product){
            $q->where('id', $product->id);
        })->first(); 
        $product_list_favorites->push([
             'id' => $product->id,
             'variation_id' => $variationAttribute->variation_id ,
             'name' => $product->getTranslation('name',\App::getLocale()),
             'percentage_of_rating' => $product->percentage_of_rating,
             'number_of_raters' => $product->number_of_raters,
             'price' => $product->price,
             'isFavorite' => $product->is_favorite,
             'image_url' => $product->image_url,
             'province' => $vendorForDistance->user->province->getTranslation('name',\App::getLocale()),
             'country' => $vendorForDistance->user->province->country->getTranslation('name',\App::getLocale()),
             'distance' => $distance
           ]);
         }
       return response()->json([
         'data' => 
          $product_list_favorites
         
        ]);
    }

    public function cartPage(Request $request){
            $user = auth()->guard('api')->user();
            $order_details =  collect([]);
            $vendor_details =  collect([]);
            $order = \Modules\Products\Entities\Orders::with('order_details.product')
            ->where('checkout_status', null)
            ->where('buyer_id', $user->id)
            ->first();
            if(!$order){
                return response()->json(['message' => 'Make order'],405);
            }
            $orderLocation =json_decode($order->location);
            if(!$order->seller_id){
                return response()->json([
                    'is empty'
                ],403);
            }
            $vendor= $this->SpesficVendorsById($orderLocation->lat, $orderLocation->long,$order->seller_id);
            $products_in_order =  collect([]);
            $products = $order->order_details;
            foreach ($products as $product_details) {
            $product =  $product_details->product;
            $products_in_order->push([
                'id' => $product->id,
                'variation_id' => $product_details->variation_id ,
                'name' => $product->getTranslation('name',\App::getLocale()),
                'total' => $product_details->total,
                'order_id' => $product_details->order_id,
                'quantity' => $product_details->quantity,
                'image_url' => $product->image_url,
                'province' => $vendor->user->province->getTranslation('name',\App::getLocale()),
                'country' => $vendor->user->province->country->getTranslation('name',\App::getLocale()),
                'distance' => $vendor->distance,
            ]);
        
        }
        $order_details->push([
        'id' => $order->id,
        'total' => $order->total,
        'after_discount' => $order->after_discount,
        'order_details' =>  $products_in_order,
        'can_checkout' => $vendor->status_id == 1 ? true : false,
        ]);
       return response()->json([
         'data' => $order_details
        ]);
    }
    public function categories(){
       $categories = \Modules\Vendors\Entities\TypeOFVendor::get();
       $categories_list = collect([]);
        foreach($categories as $category){
            $categories_list->push([
                'id' => $category->id,
                'name' => $category->getTranslation('name', \App::getLocale()),
                'image' => $category->vendor_type_image_url
            ]);
        }
        return response([
            'data' => $categories_list
        ]);
    }
    public function getPrice(Request $request){
       $request->validate([
            'product_id' => 'required',
            'attributes' => 'required'
       ]);
       $product = \Modules\Products\Entities\Product::whereId($request->product_id)->first();
          if(!$product){
            return response()->json([
                'message' =>  'Not Found'
                ]);
          }
        $attributes = json_decode($request['attributes']);
       $count = 0;
       if($attributes->value1){
        $count++;
       }
       
       if($attributes->value2){
        $count++;
       }
        $variationProduct = \Modules\Products\Entities\ProductVariation::with('attributes')
        ->where('product_id', $request->product_id)
        ->whereHas('attributes', function($q)use($attributes){
            if($attributes->value1){
                $q->where('type_id', $attributes->type_id1)
                ->where('value',  $attributes->value1);
            }
        
        })
        ->whereHas('attributes', function($q)use($attributes){
            if($attributes->value2){
                $q->where('type_id', $attributes->type_id2)
                ->where('value',  $attributes->value2);
            }
            
        })
        ->get();
        $product = collect([]);
        foreach ($variationProduct as $variation) {
            $typesIdes = collect([]);
            $attributes = $variation->attributes;
            foreach($attributes as $attribute){

                $typesIdes->push($attribute->type_id);
            }
            if(count($typesIdes)  == $count ){
            if($variation->quantity > 0 || $variation->quantity === null){
                $product->push($variation);
            }
            }
            
        }
        if(count($product) > 1){
            return response()->json([
                'message' =>  'please make allocate more'
            ],403);
        }
        if(count($product) == 0){
            return response()->json([
                'message' =>  'Not Avilable'
            ],403);
        }
        return response()->json([
            'data' =>  [
                'product_id' => $product[0]->product_id,
                'variation_id' => $product[0]->id,
                'price' => $product[0]->price
            ]
        ]);

    }

    public function vendorMenuCategory($id){
        $menu = \Modules\Products\Entities\Category::whereHas('products', function($q) use($id) {
            $q->whereHas('vendor', function($qn) use($id){
                $qn->where('id', $id);
            });
        })->get();
        return $menu;
    }
  
}

