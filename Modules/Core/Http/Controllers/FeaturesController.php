<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FeaturesController extends Controller{
    public function __construct(){
        $this->middleware(['auth']);
    }
    public function index(){
        \Auth::user()->authorize('core_feature_manage');
        return \Modules\Core\Entities\TypeOfFeature::get();
    }
    public function store(Request $request){
        \Auth::user()->authorize('core_feature_store');
        $request->validate([
            'typeable_id' => 'required',
            'typeable_type' => 'required',
            'stating_date' => 'required',
            'ended_date' => 'required',
        ]);
        
        \DB::beginTransaction();
        try {
            $feature = \Modules\Core\Entities\Feature::where('typeable_id', $request->typeable_id)
            ->where('typeable_type', $request->typeable_type)
            ->first();
            if(!$feature){
                $feature = new \Modules\Core\Entities\Feature;
            }
            $feature->typeable_id = $request->typeable_id;
            $feature->typeable_type = $request->typeable_type;
            $feature->stating_date = $request->stating_date;
            $feature->ended_date = $request->ended_date;
            $feature->feature_type = $request->feature_type;
            $feature->created_by = \Auth::user()->id;
            $feature->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
        
    } 
    public function show(Request $request,$id){
        \Auth::user()->authorize('core_feature_manage');
        $request->validate([
            'typeable_type' => 'required',
            'feature_type' => 'required',
            
        ]);
        $feature = \Modules\Core\Entities\Feature::
        where('typeable_id', $id)
        ->where('typeable_type', $request->typeable_type)
        ->where('feature_type', $request->feature_type)->first();
        return response()->json($feature);
    }
}
