<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kreait\Firebase;
use Kreait\Firebase\Factory;

class FirebaseController extends Controller{
    private $database;
    public function __construct(){
        $this->database = \App\Firebase\FirebaseService::connect();
    }

    public function index(){
        // $reference = $this->database->getReference('orders/' .'10');
        // $buffering_driver_id = $reference->getValue()['buffering_driver_id']
        // $this->database->getReference('orders/' .'2')
        // ->update([
        //     'status_id' => 7,
        //     'buffering_driver_id' => 5
        // ]);

        
    }

    public function store(Request $request){
        $this->database->getReference('visits/2')->set([
            'ip_address' =>  $request->ip(),
            'last_visit_redirect_id' => '2'
        ]);

        \DB::transaction();
        try{
           
            \DB::commit();
        
        }catch(\Exception $e){
            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }
        return response()->json(['message' => 'ok']);
    }
}
?>

<script src="../../../../public/firebase/firebase-app.js"></script>
<script src="../../../../public/firebase/firebase-auth.js"></script>
<script src="../../../../public/firebase/firebase-database.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(function(){

        const firebaseConfig = {
            apiKey: "AIzaSyDeNq6gm8S7twr74cOAOXszis2e80nerb0",
            authDomain: "sonic-cd89a.firebaseapp.com",
            databaseURL: "https://sonic-cd89a-default-rtdb.firebaseio.com",
            projectId: "sonic-cd89a",
            storageBucket: "sonic-cd89a.appspot.com",
            messagingSenderId: "921820502132",
            appId: "1:921820502132:web:a54a9d66d0103f0a6f1921"
        };

        firebase.initializeApp(firebaseConfig);


        firebase.database().ref('orders').on('value', function(snapshot) {
            var visit_ids =[];   
            var childKey;
            snapshot.forEach(function(childSnapshot) {
                childKey = childSnapshot.key;
	            var childData = childSnapshot.val();
                console.log(childKey, childData);
              
            });
        });
    });
</script>

