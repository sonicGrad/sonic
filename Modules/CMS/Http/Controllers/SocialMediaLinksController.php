<?php

namespace Modules\CMS\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class SocialMediaLinksController extends Controller{
    public function __construct(){
        $this->middleware(['auth'])->except('index');
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
    public function manage(Request $request){
        \Auth::user()->authorize('cms_module_social_media_links_manage');

        $data['activePage'] = ['cms' => 'social_media_links'];
        $data['breadcrumb'] = [
            ['title' => 'Social Media Links'],
        ];
        $data['addRecord'] = ['href' => route('social_media_links.create')];
        if ($request->ajax()) {
            $data = \Modules\CMS\Entities\SocialMediaLink::select('*');
            return Datatables::of($data)
            ->addColumn('action', function ($social_media_link) {
                $btn = '';
                $btn .= '<a data-action="edit" href=' .route('social_media_links.edit', $social_media_link->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
                $btn .= '<a data-action="destroy" data-id='. $social_media_link->id. '  class="btn btn-xs red p-2  tooltips"><i class="fa fa-times" aria-hidden="true"></i> </a>';
                return $btn;
            })
            ->addColumn('link', function ($social_media_link) {
                $btn = '';
                $btn .= '<a  target = "_blank" href=' .$social_media_link->content .' style="margin :10px" class="btn btn-xs btn-primary">'.$social_media_link->content.' </a>';
                return $btn;
            })
            ->rawColumns(['action','link'])
            ->filter(function ($query) use ($request) {
                if ($request->has('type_en') && $request->get('type_en') != null) {
                    $query->where('type->en', 'like', "%{$request->get('type_en')}%");
                }
                if ($request->has('type_ar') && $request->get('type_ar') != null) {
                    $query->where('type->ar', 'like', "%{$request->get('type_ar')}%");
                }
            })
            ->toJson();
        }

        return view('cms::social_media_links',[
            'data' => $data,
        ]);
    }
    public function create(){
        \Auth::user()->authorize('cms_module_social_media_links_manage');

        $data['activePage'] = ['cms' => 'social_media_links'];
        $data['breadcrumb'] = [
            ['title' => 'Social Media Links'],
            ['title' => 'Add Social Media Link'],

        ];
        return view('cms::create-social_media_links',[
            'data' => $data,
        ]);
    } 
    public function store(Request $request){
        \Auth::user()->authorize('cms_module_social_media_links_store');

        $request->validate([
            'type_ar' => 'required',
            'type_en' => 'required',
            'content' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $type = \Modules\CMS\Entities\SocialMediaLink::where('type', $request->type_en)->first();
            if($type){
                return response()->json(['message' =>'This Data Are Already Exisit'],403);
            }
            $type = \Modules\CMS\Entities\SocialMediaLink::where('type', $request->type_ar)->first();
            if($type){
                return response()->json(['message' =>'هذه البيانات موجودة مسبقا'],403);
            }
            $type = new \Modules\CMS\Entities\SocialMediaLink;
            $type
            ->setTranslation('type', 'en',  $request->type_en)
            ->setTranslation('type', 'ar',   $request->type_ar);
            $type->content = $request->content;
            $type->created_by  = \Auth::user()->id;

            $type->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
        
    }
    public function edit($id){
        \Auth::user()->authorize('cms_module_social_media_links_manage');

        $type = \Modules\CMS\Entities\SocialMediaLink::whereId($id)->first();
        $data['activePage'] = ['cms' => 'social_media_links'];
        $data['breadcrumb'] = [
            ['title' => 'Social Media Links'],
            ['title' => 'Edit Social Media Link'],
        ];
        return view('cms::edit-social_media_links',[
            'data' => $data,
            'type' => $type
        ]);
    }
    public function update(Request $request, $id){
        \Auth::user()->authorize('cms_module_social_media_links_update');

        $request->validate([
            'type_ar' => 'required',
            'type_en' => 'required',
            'content' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $type = \Modules\CMS\Entities\SocialMediaLink::where('id','<>', $id)->where('type', $request->type_en)->first();
            if($type){
                return response()->json(['This Data Are Already Exisit'],403);
            }
            $type =  \Modules\CMS\Entities\SocialMediaLink::whereId($id)->first();
            $type
            ->setTranslation('type', 'en',  $request->type_en)
            ->setTranslation('type', 'ar',   $request->type_ar);
            $type->content = $request->content;
            $type->save();

            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
        
    }
    public function destroy(Request $request, $id){
        \Auth::user()->authorize('cms_module_social_media_links_destroy');

        \Modules\CMS\Entities\SocialMediaLink::destroy($id);
        return response()->json('Ok',200);
    }
}
