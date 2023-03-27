<?php

namespace Modules\CMS\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class TermsController extends Controller{
    public function __construct(){
        $this->middleware(['auth'])->except(['about_us', 'privacy_policy','terms']);
    }
    public function index(){
        $data = \Modules\CMS\Entities\Term::get();
        return response()->json($data);
    }
    public function manage(Request $request){
        \Auth::user()->authorize('cms_module_terms_manage');

        $data['activePage'] = ['cms' => 'terms'];
        $data['breadcrumb'] = [
            ['title' => 'Terms & About Us & Policies'],
        ];
        // $data['addRecord'] = ['href' => route('terms.create')];
        if ($request->ajax()) {
            $data = \Modules\CMS\Entities\Term::select('*');
            return Datatables::of($data)
            ->addColumn('action', function ($term) {
                $btn = '';
                $btn .= '<a data-action="edit" href=' .route('terms.edit', $term->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
                $btn .= '<a data-action="destroy" data-id='. $term->id. '  class="btn btn-xs red p-2  tooltips"><i class="fa fa-times" aria-hidden="true"></i> </a>';
                return $btn;
            })
            ->addColumn('modalEn', function ($term) {
                $btn = '';
                $btn .= '<button type="button" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#descEn' . $term->id . '">';
                // $btn .= '<i class="fa-solid fa-eye"></i>';
                $btn .= "Show";
                $btn .= '</button>';
                $btn .= '<div class="modal fade" id="descEn'. $term->id. '" tabindex="-1" role="dialog" aria-labelledby="descEn'. $term->id.'" aria-hidden="true">';
                $btn .= '  <div class="modal-dialog" role="document">';
                $btn .= '<div class="modal-content">';
                $btn .= '<div class="modal-header">';
                $btn .= '<h5 class="modal-title" id="">content</h5>';
                $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                $btn .= '<div class="modal-body">';
                $btn .=$term->getTranslations('content')['en'];
                $btn .= '</div>';
                $btn .= ' <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                $btn .= '</div></div></div></div>';
                return $btn;
            })
            ->addColumn('modalAr', function ($term) {
                $btn = '';
                $btn .= '<button type="button" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#descAr' . $term->id . '">';
                // $btn .= '<i class="fa-solid fa-eye"></i>';
                $btn .= "Show";

                $btn .= '</button>';
                $btn .= '<div class="modal fade" id="descAr'. $term->id. '" tabindex="-1" role="dialog" aria-labelledby="descAr'. $term->id.'" aria-hidden="true">';
                $btn .= '  <div class="modal-dialog" role="document">';
                $btn .= '<div class="modal-content">';
                $btn .= '<div class="modal-header">';
                $btn .= '<h5 class="modal-title" id="">content</h5>';
                $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                $btn .= '<div class="modal-body">';
                $btn .= $term->getTranslations('content')['ar'];
                $btn .= '</div>';
                $btn .= ' <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                $btn .= '</div></div></div></div>';
                return $btn;
            })
            ->rawColumns(['action', 'modalEn' ,'modalAr'])
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

        return view('cms::terms',[
            'data' => $data,
        ]);
    }

    public function edit($id){
        \Auth::user()->authorize('cms_module_terms_manage');

        $type = \Modules\CMS\Entities\Term::whereId($id)->first();
        $data['activePage'] = ['cms' => 'terms'];
        $data['breadcrumb'] = [
            ['title' => 'Terms & About Us & Policies'],
            ['title' => 'Edit Data'],
        ];
        return view('cms::edit-terms',[
            'data' => $data,
            'type' => $type
        ]);
    }
    public function update(Request $request, $id){
        \Auth::user()->authorize('cms_module_terms_update');

        $request->validate([
            'type_ar' => 'required',
            'type_en' => 'required',
            'content_ar' => 'required',
            'content_text_ar' => 'required',
            'content_en' => 'required',
            'content_text_en' => 'required',
        ]);

        \DB::beginTransaction();
        try {
            $type = \Modules\CMS\Entities\Term::where('id','<>', $id)->where('type', $request->type_en)->first();
            if($type){
                return response()->json(['This Data Are Already Exisit'],403);
            }
            $type =  \Modules\CMS\Entities\Term::whereId($id)->first();
            $type
            ->setTranslation('type', 'en',  $request->type_en)
            ->setTranslation('type', 'ar',   $request->type_ar);
            $type
            ->setTranslation('content', 'en',  $request->content_en)
            ->setTranslation('content', 'ar',   $request->content_ar);
            $type
            ->setTranslation('content_text', 'en',  $request->content_text_en)
            ->setTranslation('content_text', 'ar',   $request->content_text_ar);
            $type->save();

            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
        
    }
    public function destroy(Request $request, $id){
        \Auth::user()->authorize('cms_module_terms_destroy');

        \Modules\CMS\Entities\Term::destroy($id);
        return response()->json('Ok',200);
    }
    public function show($id){
        $type = \Modules\CMS\Entities\Term::whereId($id)->first();
        return response()->json($type);
    }
    public function about_us(){
        $type = \Modules\CMS\Entities\Term::whereId('1')->first();
        return response()->json($type);
    }
    public function privacy_policy(){
        $type = \Modules\CMS\Entities\Term::whereId('2')->first();
        return response()->json($type);
    }
    public function terms(){
        $type = \Modules\CMS\Entities\Term::whereId('3')->first();
        return response()->json($type);
    }
}
