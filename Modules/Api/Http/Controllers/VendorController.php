<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VendorController extends Controller{
  use \App\Traits\NearestVendors;

    public function __construct(){
        $this->middleware('auth:api', [
        'except' => []
      ]);
    }
    public function viewAllTypesOfVendorsUnderCategoryOfVendors(Request $request, $id){
        $request->validate([
            'order_id' => 'required'
        ]);
        $user = auth()->guard('api')->user();
        $order = \Modules\Products\Entities\Orders::whereId($request->order_id)->where('buyer_id', $user->id)->first();
        if(!$order){
            return response()->json([
                'message' => 'Not Allowed This Order Not For This User'
            ],403);
        }
       $orderLocation =json_decode($order->location);
        $nearestVendors = $this->NearestVendorsByType($orderLocation->lat , $orderLocation->long, $id , $user->province_id );
        $vendorsNearestList = collect([]);
        foreach ($nearestVendors as $restaurant) {
            $vendorsNearestList->push([
                'id' => $restaurant->id,
                'name' =>  $restaurant->company_name,
                'image_url' => $restaurant->vendor_logo_url,
                'distance' => $restaurant->distance,
                'time' => intval($restaurant->distance/1000/30*60) . ' to ' .intval($restaurant->distance/1000/60*60)
            ]);
        }
        $allVendors = $this->NearestVendorsByTypeWithoutRadius($orderLocation->lat , $orderLocation->long,$id);
        $vendorsList = collect([]);
        foreach ($allVendors as $restaurant) {
            $vendorsList->push([
                'id' => $restaurant->id,
                'company_name' =>  $restaurant->company_name,
                'image_url' => $restaurant->vendor_logo_url,
                'location' => json_decode( $restaurant->location) ,
                'open' => $restaurant->starting_time,
                'close' => $restaurant->closing_time,
                'address' => $restaurant->user->address,
                'province' => $restaurant->user->province->getTranslation('name', \App::getLocale()),
                'country' => $restaurant->user->province->country->getTranslation('name', \App::getLocale()),
                'rating' => $restaurant->user->percentage_of_rating,
                'number_of_raters' => $restaurant->user->number_of_raters,
                'email' => $restaurant->user->email,
                'mobile_no' => $restaurant->user->mobile_no,
                'distance' => $restaurant->distance,
                'time' => intval($restaurant->distance/1000/30*60 ). ' to ' .intval($restaurant->distance/1000/60*60) 
            ]);
       }

        return response()->json([
          'data' => [
              'vendors_nearest' => $vendorsNearestList,
              'allVendors' => $vendorsList,
          ]
      ]);
    }
    public function viewAllTypesOfVendorsUnderCategoryOfVendorsForSearch(Request $request, $id){
      $request->validate([
        'order_id' => 'required'
      ]);
      $user = auth()->guard('api')->user();
      $order = \Modules\Products\Entities\Orders::whereId($request->order_id)->where('buyer_id', $user->id)->first();
        if(!$order){
            return response()->json([
                'message' => 'Not Allowed This Order Not For This User'
            ],403);
        }
       $orderLocation =json_decode($order->location);
        // $nearestVendors = $this->NearestVendorsByType($orderLocation->lat , $orderLocation->long, $id , $user->province_id );
        // $vendorsNearestList = collect([]);
        // foreach ($nearestVendors as $restaurant) {
        //     $vendorsNearestList->push([
        //         'id' => $restaurant->id,
        //         'name' =>  $restaurant->company_name,
        //         'image_url' => $restaurant->vendor_logo_url,
        //     ]);
        // }

        $categoriesList = collect([]);
        $categories = \Modules\Products\Entities\Category::where('vendor_type', $id)->get();
        foreach ($categories as $category) {
            $categoriesList->push([
                'id' => $category->id,
                'name' =>  $category->getTranslation('name',\App::getLocale()),
                'image_url' => $category->image_url,
            ]);
        }
        
        return response()->json([
          'data' => [
            // 'nearest_vendors' =>  $vendorsNearestList,
            'categories' =>  $categoriesList,
          ]
      ]);
    }
    public function filleterSearchPage(){
      $categories = \Modules\Vendors\Entities\TypeOFVendor::get();
      $data = collect([]);
      foreach ($categories as $category) {
        $data->push([
          'id' => $category->id,
          'name' => $category->getTranslation('name',\App::getLocale()),
          'image' => $category->vendor_type_image_url,
        ]);
      }
      return response()->json(['data' => $data]);
    }
    public function vendorDetails(Request $request, $id){
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
        $vendor= $this->SpesficVendorsById($orderLocation->lat, $orderLocation->long,$id);
        // if($vendor->type_id == 3){
        //   $product_list =  collect([]);
        //   foreach ($vendor->products as $product) {
        //     $product_list->push([
        //       'id' => $product->id,
        //       'name' => $product->getTranslation('name',\App::getLocale()),
        //       'percentage_of_rating' => $product->percentage_of_rating,
        //       'number_of_raters' => $product->number_of_raters,
        //       'price' => $product->price,
        //       'image_url' => $product->image_url,
        //       'is_favorite' => $product->is_favorite
        //     ]);
        //   } 
        //   return response()->json(['data' => $product_list]);
        // }

        $vendor_detailed = [
        'company_name' => $vendor->company_name,
        'location' => json_decode($vendor->location),
        'image_url' => $vendor->vendor_logo_url,
        'percentage_of_rating' => $vendor->user->percentage_of_rating,
        'number_of_raters' => $vendor->user->number_of_raters,
        
        'mobile_no' => $vendor->user->mobile_no,
        'province' => $vendor->user->province->getTranslation('name',\App::getLocale()),
        'country' => $vendor->user->province->country->getTranslation('name',\App::getLocale()),
      ];
      // return \Modules\Products\Entities\Product::whereId('5')->first();
      $product_list =  collect([]);
      foreach ($vendor->products as $product) {
        $product_list->push([
          'id' => $product->id,
          'name' => $product->getTranslation('name',\App::getLocale()),
          'percentage_of_rating' => $product->percentage_of_rating,
          'number_of_raters' => $product->number_of_raters,
          'price' => $product->price,
          'image_url' => $product->image_url,
          'province' => $vendor->user->province->getTranslation('name',\App::getLocale()),
          'country' => $vendor->user->province->country->getTranslation('name',\App::getLocale()),
          'is_favorite' => $product->is_favorite
        ]);
      } 
      $vendor_data= $this->SpesficVendorsByIdWithProductFeature($orderLocation->lat, $orderLocation->long,$id,'2');
    
      $product_list_for_vendor=  $vendor_data->products->where('type' , '<>', null)->where('type.feature_type', '2');
      $product_list_most_papular =  collect([]);
      foreach ($product_list_for_vendor as $product) {
        $product_list_most_papular->push([
          'id' => $product->id,
          'name' => $product->getTranslation('name',\App::getLocale()),
          'percentage_of_rating' => $product->percentage_of_rating,
          'number_of_raters' => $product->number_of_raters,
          'price' => $product->price,
          'image_url' => $product->image_url,
          'province' => $vendor->user->province->getTranslation('name',\App::getLocale()),
          'country' => $vendor->user->province->country->getTranslation('name',\App::getLocale()),
          'distance' => $vendor->distance,
          'is_favorite' => $product->is_favorite

        ]);
      }
      $favorites =  \Modules\Users\Entities\Favorite::with('product.vendor')->where('user_id', $user->id)
      ->whereHas('product', function($q) use($vendor_data){
        $q->where('vendor_id', $vendor_data->id);
      })
      ->get();
      $product_list_favorites =  collect([]);
      foreach ($favorites as $products) {
        $product_list_favorites->push([
            'id' => $products->product->id,
            'name' => $products->product->getTranslation('name',\App::getLocale()),
            'percentage_of_rating' => $products->product->percentage_of_rating,
            'number_of_raters' => $products->product->number_of_raters,
            'price' => $products->product->price,
            'image_url' => $product->image_url,
            'province' => $vendor->user->province->getTranslation('name',\App::getLocale()),
            'country' => $vendor->user->province->country->getTranslation('name',\App::getLocale()),
            'distance' => $vendor->distance,
            'is_favorite' => $product->is_favorite

          ]);
        }
        $offersList = collect([]);
        $offers = \Modules\Vendors\Entities\Offer::where('vendor_id', $vendor->id)->get();
        foreach($offers as $offer){
          $offersList->push([
            'id'=> $offer->id,
            'name'=> $offer->getTranslation('name',\App::getLocale()),
            'image_url'=> $offer->image_url
          ]);
        }
      return response()->json([
        'data' => [
          'offers' => $offersList,
          'vendor_detailed' => $vendor_detailed,
          'menu' =>$product_list,
          'product_most_papular' => $product_list_most_papular,
          'favorites' => $product_list_favorites
        ]
      ]);

    }
  
    
   
    
}
