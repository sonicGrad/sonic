<?php

namespace Modules\CMS\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class ContactUsController extends Controller{
    public function __construct(){
        $this->middleware(['auth'])->except('storeApi');
    }
    public function index(){
        $data = \Modules\CMS\Entities\ContactUs::get();
        return response()->json($data);
    }
    public function storeApi(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'mobile_no' => 'required',
            'content' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $newContact = new \Modules\CMS\Entities\ContactUs;
            $newContact->name = $request->name;
            $newContact->email = $request->email;
            $newContact->mobile_no = $request->mobile_no;
            $newContact->content = $request->content;
            $newContact->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
    }
    public function manage(Request $request){
        \Auth::user()->authorize('cms_module_contact_us_manage');

        $data['activePage'] = ['cms' => 'contact_us'];
        $data['breadcrumb'] = [
            ['title' => 'Contact Us'],
        ];
        // $data['addRecord'] = ['href' => route('contact_us.create')];
        if ($request->ajax()) {
            $data = \Modules\CMS\Entities\ContactUs::select('*');
            return Datatables::of($data)
            ->addColumn('action', function ($contact_us) {
                $btn = '';
                $btn .= '<a data-action="edit" href=' .route('contact_us.reply', $contact_us->id) . ' style="margin :10px" class="btn btn-xs btn-primary"><i class="icon-only ace-icon fa fa-share"></i></i> </a>';
                // $btn .= '<a data-action="destroy" data-id='. $contact_us->id. ' style="margin :10px" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> </a>';
                return $btn;
                
            })
            ->addColumn('content', function ($contact_us) {
                $btn = '';
                $btn .= '<button type="button" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#descAr' . $contact_us->id . '">';
                $btn .= "Show";

                $btn .= '</button>';
                $btn .= '<div class="modal fade" id="descAr'. $contact_us->id. '" tabindex="-1" role="dialog" aria-labelledby="descAr'. $contact_us->id.'" aria-hidden="true">';
                $btn .= '  <div class="modal-dialog" role="document">';
                $btn .= '<div class="modal-content">';
                $btn .= '<div class="modal-header">';
                $btn .= '<h5 class="modal-title" id="">Content OF Message</h5>';
                $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                $btn .= '<div class="modal-body">';
                $btn .= $contact_us->content;
                $btn .= '</div>';
                $btn .= ' <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                $btn .= '</div></div></div></div>';
                return $btn;
            })
            ->addColumn('reply', function ($contact_us) {
                $btn = '';
                $btn .= '<button type="button" class="btn btn-sm btn-primary"  data-toggle="modal" data-target="#descEn' . $contact_us->id . '">';
                $btn .= "Show";
                $btn .= '</button>';
                $btn .= '<div class="modal fade" id="descEn'. $contact_us->id. '" tabindex="-1" role="dialog" aria-labelledby="descEn'. $contact_us->id.'" aria-hidden="true">';
                $btn .= '  <div class="modal-dialog" role="document">';
                $btn .= '<div class="modal-content">';
                $btn .= '<div class="modal-header">';
                $btn .= '<h5 class="modal-title" id="">Reply Message</h5>';
                $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                $btn .= '<div class="modal-body">';
                $btn .=$contact_us->reply_msg;
                $btn .= '</div>';
                $btn .= ' <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                $btn .= '</div></div></div></div>';
                return $btn;
            })
            ->rawColumns(['action','reply','content'])
            ->filter(function ($query) use ($request) {
                if ($request->has('name') && $request->get('name') != null) {
                    $query->where('name', 'like', "%{$request->get('name')}%");
                }
                if ($request->has('email') && $request->get('email') != null) {
                    $query->where('email', 'like', "%{$request->get('email')}%");
                }
                if ($request->has('mobile_no') && $request->get('mobile_no') != null) {
                    $query->where('mobile_no', 'like', "%{$request->get('mobile_no')}%");
                }
            })
            ->toJson();
        }

        return view('cms::contact_us',[
            'data' => $data,
        ]);
    }
    public function reply($id){
        \Auth::user()->authorize('cms_module_contact_us_manage');
        $type = \Modules\CMS\Entities\ContactUs::whereId($id)->first();
        $data['activePage'] = ['cms' => 'contact_us'];
        $data['breadcrumb'] = [
            ['title' => 'Contact Us'],
            ['title' => 'Reply MSG'],
        ];
        return view('cms::reply-contact_us',[
            'data' => $data,
            'type' => $type
        ]);
    }
    public function update(Request $request, $id){
        \Auth::user()->authorize('cms_module_contact_us_update');
        $request->validate([
            'reply' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $type =  \Modules\CMS\Entities\ContactUs::whereId($id)->first();
            $type->reply_msg = $request->reply;
            if($request->reply){
                $type->is_read = '2';
            }
            $type->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok']);
        
    }
    public function show($id){
        $type = \Modules\CMS\Entities\ContactUs::whereId($id)->first();
        return response()->json($type);
    }
}
