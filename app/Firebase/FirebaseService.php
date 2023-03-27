<?php

namespace App\Firebase;

use Illuminate\Http\Request;
use Kreait\Firebase;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseService{
    
    public static function connect(){
        $firebase = (new Factory)
            ->withServiceAccount(__DIR__.'/sonic-cd89a-firebase-adminsdk-tmyt8-5c9ce02972.json')
            ->withDatabaseUri('https://sonic-cd89a-default-rtdb.firebaseio.com');
 
        $database = $firebase->createDatabase();
        return $database;
    }
 


}
