<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Auth;
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class JWTController extends Controller{

    public function __construct(){
        $this->middleware('auth:api', [
        'except' => [
        'login', 'register',
        'registerPage','sendCode','checkCode' ,'changePassword'
        ]
     ]);
    }
    public function index(){
        $links = \Modules\CMS\Entities\SocialMediaLink::get();
        $data = collect([]);
       foreach($links as $link){
        $data->push([
            'name' =>  $link->getTranslation('type',\App::getLocale()),
            'content' =>  $link->content,
        ]);
       }
        return response()->json($data);
    }
    public function registerPage(Request $request){
        $countries = \Modules\Core\Entities\Country::get();
        $cites = \Modules\Core\Entities\CountryProvince::get();
        $data_county = collect([]);
        $data_city = collect([]);
        foreach($countries as $country){
            $data_county->push([
                'id' => $country->id,
                'name' =>  $country->getTranslation('name',\App::getLocale()),
            ]);
        }
        foreach($cites as $city){
            $data_city->push([
                'id' => $city->id,
                'country_id' => $city->country_id,
                'name' =>  $city->getTranslation('name',\App::getLocale()),
            ]);
        }
        return response()->json([
            'data' => [
                'countries' => $data_county,
                'cites' => $data_city
            ]
        ]);
    }
    public function register(Request $request){
        $request->validate([
            'username' => 'required|string|min:2|max:100',
            'email' => 'required',
            'password' => 'required|string',
            // 'confirm_password' => 'required|same:password',
            'mobile_no' => 'required|string',
        ]);
        if($request->type == 3){
            $request->validate([
              'company_name' => 'required',
              'location' => 'required',
              'type_of_vendor' => 'required',
            //   'description' => 'required',
            ]);
        }
        if(strlen($request->password) < 6){
            return response()->json(['message' => 'This password must be at least 6 characters'],403 );
        }
        \DB::beginTransaction();
        try {
            $mobile_no = \Modules\Users\Entities\User::where('mobile_no', $request->mobile_no)->first();
            if($mobile_no){
                return response()->json(['message' => 'This mobile number is already exsist'],403 );
            }
            $email = \Modules\Users\Entities\User::where('email', $request->email)->first();
            if($email){
                return response()->json(['message' => 'This mobile number is already exsist'],403 );
            }
            $user = new \Modules\Users\Entities\User;
            $user->first_name = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->province_id  = $request->province_id ;
            $user->mobile_no  = $request->mobile_no ;
            $user->status_id   = '1';
            if($request->type == 3 ){
                $vendor = new \Modules\Vendors\Entities\Vendors();
                $vendor->location = $request->location;
                $vendor->company_name = $request->company_name;
                $vendor->type_id = $request->type_of_vendor;
                $vendor->status_id = '2';
                $vendor->user_id = $user->id;
                $vendor->save();
                $user->save();
                // if($request->bran)
                \DB::commit();
                return response()->json(['message' => 'Please wait, your account will be reviewed within 24 hours', 'data'=> $vendor] );
            }
            $user->save();
            $array= [];
            $array['mobile_no'] = $request->mobile_no;
            $array['password'] = $request->password;
            if (!$token = auth()->guard('api')->attempt($array)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }
        $user->setAttribute('token', $this->respondWithToken($token));
        return response()->json([
            'data' =>$user,
        ], 201);
       
    }

    public function login(Request $request){
       $request->validate([
        'mobile_no' => 'required',
        'password' => 'required|string',
       ]);
       if(strlen($request->password) < 6){
        return response()->json(['message' => 'This password must be at least 6 characters'],403 );
        }
       if($request->type == 3){
        $vendor = \Modules\Vendors\Entities\Vendors::whereHas('user', function($q)use($request){
            $q->where('mobile_no',$request->mobile_no );
        })->first();
        if(!$vendor){
            return response()->json(['message' => 'Not allow'], 403);
        }
       }
       $array= [];
       $array['mobile_no'] = $request->mobile_no;
       $array['password'] = $request->password;
        if (!$token = auth()->guard('api')->attempt($array)) {
            return response()->json(['message' => 'Your Password Or Email Not Correct'], 403);
        }
        $user = \Modules\Users\Entities\User::where('mobile_no', $request->mobile_no)->first();
        $user->setAttribute('token', $this->respondWithToken($token));
        return response()->json([
            'data' =>$user,
        ], 201);
    }
    public function sendCode(Request $request){
        \DB::beginTransaction();
        try {
        $code = rand(1111, 9999);
        if($request->email){
            $user = \Modules\Users\Entities\User::where('email', $request->email)->first();
            if(!$user){
                return response()->json([
                    'message' => 'This User Not Exisit Please Register'
                ],403);
            }
        }
        if($request->mobile_no){
            $user = \Modules\Users\Entities\User::where('mobile_no', $request->mobile_no)->first();
            if(!$user){
                return response()->json([
                    'message' => 'This User Not Exisit Please Register'
                ]);
            }
            $otp = new \Modules\Users\Entities\Otp;
            $otp->mobile_no = $request->mobile_no;
            $otp->code = $code;
            $otp->user_id  = $user->id;
            $otp->message = 'Your Verification Code is: ' .$code;
            $otp->save();
            $user->notify(new \Modules\Api\Notifications\ForgetPasswordViaPhoneNumber($user,$code));
        }

        if($request->email){
            $user->notify(new \Modules\Api\Notifications\ForgetPasswordCode($user, $code));
            $otp = new \Modules\Users\Entities\Otp;
            $otp->mobile_no = $request->mobile_no;
            $otp->code = $code;
            $otp->user_id  = $user->id;
            $otp->message = 'Your Verification Code is: ' .$code;
            $otp->save();
            // return response()->json([
            //     'message' => 'send Code',
            //     'user_id' => $user->id
            // ]);
        }
        // if($request->mobile_no){
        //     $otp = new \Modules\Users\Entities\Otp;
        //     $otp->mobile_no = $request->mobile_no;
        //     $otp->code = $code;
        //     $otp->user_id  = $user->id;
        //     $otp->message = 'Your Verification Code is: ' .$code;
        //     $otp->save();
        //     return response()->json([
        //         'message' => 'send Code',
        //         'user_id' => $user->id
        //     ]);
        // }
        \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }
        return response()->json([
            'message' => 'send Code',
            'user_id' => $user->id
        ],200);
    }
    public function checkCode(Request $request){
        $request->validate([
            'code' => 'required',
            'user_id' => 'required'
        ]);
        if(!\Modules\Users\Entities\User::whereId($request->user_id)->first()){
            return response()->json([
                'message' => 'This User Not Found'
            ],403);
        }
        $otp = \Modules\Users\Entities\Otp::where('user_id', $request->user_id)->latest()->first();
        if($request->code == trim($otp->code)){
            $otp->verify = '1';
            $otp->save();
            return response()->json([
                'message' => 'Verified'
            ]);

        }
        return response()->json([
            'message' => 'Not Verified'
        ],403);

    }
    public function changePassword(Request $request){
        $request->validate([
            'password' => 'required',
            'confirm_password' => 'required',
            'user_id' =>'required'
        ]);
       $otp = \Modules\Users\Entities\Otp::where('user_id', $request->user_id)->latest()->first();
       if($otp->verify != 1){
        return response()->json([
            'message' => 'Not Allowed'
        ],403);
       }
       if(strlen($request->password) < 6){
            return response()->json([
                'message' => 'Password less than 6 characters'
            ],403);
       }
       $user = \Modules\Users\Entities\User::whereId($request->user_id)->first();
       $user->password = Hash::make($request->password) ;
       $user->save();
       $request->merge([
        'mobile_no' => $user->mobile_no
       ]);
      return  $this->login($request);
    }
    public function changePassWhenLogin(Request $request){
        $user = auth()->guard('api')->user();
        $request->validate([
            'current_password' => 'required',
            'password' => 'required',
        ]);
        if($request->password != $request->confirm_password){
            return response()->json(['message' => 'The confirm password and password not match'],422);
        }
        if(!Hash::check($request->current_password, $user->password )){
            return response()->json(['message' => 'The Current Password Not Correct'],422);
        }
        if(strlen($request->password) < 6){
            return response()->json([
                'message' => 'Password less than 6 characters'
            ],403);
       }
        
        $user = \Modules\Users\Entities\User::whereId($user->id)->first();
        $user->password = Hash::make($request->password) ;
        $user->save();
        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $this->refresh()
            ]
        ]);
    }
    public function logout(){
        auth()->guard('api')->logout();

        return response()->json(['message' => 'User successfully logged out.']);
    }
    public function refresh(){
        return $this->respondWithToken(auth()->guard('api')->refresh());
    }
    public function profile(){
        return response()->json(auth()->guard('api')->user());
    }
    protected function respondWithToken($token){
        return $token;
        // return response()->json([
        //     'token_type' => 'bearer',
        //     // 'expires_in' => auth()->guard('api')->factory()->getTTL() * 60
        // ]);
    }

}