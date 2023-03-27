<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserController extends Controller{
    public function __construct(){
        $this->middleware('auth:api', [
        'except' => []
      ]);
    }
    public function editProfile(Request $request){
        $user = auth()->guard('api')->user();
        $request->validate([
            'username' => 'required',
            'email' => 'required',
            'mobile_no' => 'required',
        ]);
        $mobile_no = \Modules\Users\Entities\User::where('id', '<>', $user->id)->where('mobile_no', trim($request->mobile_no))->first();
        if($mobile_no){
            return response()->json(['message' =>'This Mobile Are Already Exisit'],403);
        }
        $email = \Modules\Users\Entities\User::where('id', '<>', $user->id)->where('email', trim($request->email))->first();
        if($email){
            return response()->json(['message' =>'This Email Are Already Exisit'],403);
        }
        $user->first_name = $request->username;
        $user->email = trim($request->email);
        $user->mobile_no = trim($request->mobile_no);
        $user->save();

        return response()->json([
            'message' => 'successful Update Data',
            'data' => $user
        ]);
    }
    public function coupons(){
        $user = auth()->guard('api')->user();
        $available_coupon = \Modules\Vendors\Entities\Coupon::
        whereHas('users', function($q) use($user){
            $q->where('um_user_coupons.user_id', $user->id);
        })
        ->get();
        $used_coupons = $user->coupons;

        $available_coupon_list = collect([]);
        foreach($available_coupon as $coupon){
            $available_coupon_list->push([
                'name' =>  $coupon->name,
                'description' =>  $coupon->getTranslation('description',\App::getLocale()),
                'value' =>  $coupon->value,
                'status' => $coupon->status
            ]);
        }
        
        $used_coupons_list = collect([]);
        foreach($used_coupons as $coupon){
            $used_coupons_list->push([
                'name' =>  $coupon->name,
                'description' =>  $coupon->getTranslation('description',\App::getLocale()),
                'value' =>  $coupon->value,
            ]);
        }
        return response()->json([
            'data' => [
                'available_coupon' => $available_coupon_list,
                'used_coupons' => $used_coupons_list
            ]
        ]);
    }
    public function settingPage(){
        $user = auth()->guard('api')->user();
        $countries = \Modules\Core\Entities\Country::get();
        $data_county = collect([]);
        foreach($countries as $country){
            $data_county->push([
                'id' => $country->id,
                'name' =>  $country->getTranslation('name',\App::getLocale()),
            ]);
        }

        $data_city = collect([]);
        $cites = \Modules\Core\Entities\CountryProvince::get();
        foreach($cites as $city){
            $data_city->push([
                'id' => $city->id,
                'country_id' => $city->country_id,
                'name' =>  $city->getTranslation('name',\App::getLocale()),
            ]);
        }

        $about_us = \Modules\CMS\Entities\Term::whereId('1')->first();
        $about_us = $about_us->getTranslation('content_text',\App::getLocale());

        $terms = \Modules\CMS\Entities\Term::whereId('3')->first();
        $terms = $terms->getTranslation('content_text',\App::getLocale());
        $policies = \Modules\CMS\Entities\Term::whereId('2')->first();
        $policies = $policies->getTranslation('content_text',\App::getLocale());
        return response()->json([
            'data' => [
                'countries' => $data_county,
                'cites' => $data_city,
                'about_us' => $about_us,
                'terms' => $terms,
                'policies' => $policies,
            ]
        ]);
    }

    public function Favorite(Request $request){
       $request->validate([
        'product_id' => 'required'
       ]);
       $user = auth()->guard('api')->user();
       $productFavorite = \Modules\Users\Entities\Favorite::where('product_id', $request->product_id)
       ->where('user_id', $user->id )
       ->first();
       if($productFavorite){
        \Modules\Users\Entities\Favorite::destroy($productFavorite->id);
        return response()->json(['message' => 'Delete to Favorite']);
       }
        $productFavorite = new \Modules\Users\Entities\Favorite;
        $productFavorite->user_id = $user->id;
        $productFavorite->product_id = $request->product_id;
        $productFavorite->save();
        return response()->json(['message' => ' Add to Favorite']);
    }

    public function addressBook(){
       $user = auth()->guard('api')->user();
       
       $addressBook = collect([]);
       $lang = app()->getLocale();
    //    return 
      foreach ($user->address_book as $address_book) {
        $addressBook->push([
            'id' => $address_book->id,
            'name' => $address_book->name,
            'details' => $address_book->details,
            'mobile_no' => $address_book->mobile_no,
            'location' => json_decode($address_book->location),
            'location_formate' => getLocationFromLatAndLong( json_decode($address_book->location)->lat ?? 34.620745, json_decode($address_book->location)->long ?? 34.620745, $lang),
            'created_at' => $address_book->created_at,
        ]);
      }
      return response()->json([
        'data' => $addressBook 
      ]);

    }
    public function AddAddressBook(Request $request){
        $request->validate([
            'name' => 'required',
            'location' => 'required'
        ]);
       $lang = app()->getLocale();

        $user = auth()->guard('api')->user();
        $addressBook = new \Modules\Users\Entities\AddressBook; 
        $addressBook->name = $request->name;
        $addressBook->location = $request->location;
        $addressBook->details = $request->details;
        $addressBook->user_id = $user->id ;
        $addressBook->save();
        return response()->json(['data' => [
            'id' => $addressBook->id,
            'name' => $addressBook->name,
            'details' => $addressBook->details,
            'mobile_no' => $addressBook->mobile_no,
            'location' => json_decode($addressBook->location),
            'location_formate' => getLocationFromLatAndLong( json_decode($addressBook->location)->lat ?? 34.620745, json_decode($addressBook->location)->long ?? 34.620745, $lang),
            'created_at' => $addressBook->created_at,
        ]]);
 
    }
    public function EditAddressBook($id,Request $request){
        $request->validate([
            'name' => 'required',
            'location' => 'required'
        ]);
       $lang = app()->getLocale();

        $user = auth()->guard('api')->user();
        $addressBook = \Modules\Users\Entities\AddressBook::where('user_id', $user->id)->whereId($id)->first();
        if(!$addressBook){
            return response()->json([
                'message' => 'Not Allow'
            ],403);
        }
        $addressBook->name = $request->name;
        $addressBook->location = $request->location;
        $addressBook->details = $request->details;
        $addressBook->user_id = $user->id ;
        $addressBook->save();
        return response()->json(['data' =>[
            'id' => $addressBook->id,
            'name' => $addressBook->name,
            'details' => $addressBook->details,
            'mobile_no' => $addressBook->mobile_no,
            'location' => json_decode($addressBook->location),
            'location_formate' => getLocationFromLatAndLong( json_decode($addressBook->location)->lat ?? 34.620745, json_decode($addressBook->location)->long ?? 34.620745, $lang),
            'created_at' => $addressBook->created_at,
        ]]);
 
    }
    public function removeAddressBook($id){
        $user = auth()->guard('api')->user();
        $addressBook = \Modules\Users\Entities\AddressBook::where('user_id', $user->id)->whereId($id)->first();
        if(!$addressBook){
            return response()->json([
                'message' => 'Not Allow'
            ],403);
        }
        $addressBook->delete();
        return response()->json(['message' => 'Ok']);
    }

    public function notification(){
        $user = auth()->guard('api')->user();
        $not = collect([]);
        foreach ($user->unreadNotifications as $notification) {
            $lang =  app()->getLocale();
            $not->push([
                'id' => $notification->id,
                'title' => $notification->data[$lang]['title'],
                'body' => $notification->data[$lang]['body'],
                'created_at' => $notification->created_at,
                'read_at' => $notification->read_at,
            ]);
        }
        return response()->json([ 'count' => $user->unreadNotifications->count(), 'data' => $not,]);

    }

    public function addImage(Request $request ){
        $user =auth()->guard('api')->user();
        $user = \Modules\Users\Entities\User::whereId($user->id)->first();
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $extension = strtolower($request->file('file')->extension());
            $media_new_name = strtolower(md5(time())) . "." . $extension;
            $collection = "user-image";

            $user->addMediaFromRequest('file')
                ->usingFileName($media_new_name)
                ->usingName($request->file('file')->getClientOriginalName())
                ->toMediaCollection($collection);
                $user->save();
                return response()->json([
                    'data' => $user->person_image_url
                ]);
        }
    }
}

