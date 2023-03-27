<?php

namespace Modules\Products\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redis;
use Yajra\Datatables\Datatables;

class CategoriesController extends Controller{
    public function __construct(){
        $this->middleware(['auth'])->except(['removeImage']);
    }  
    public function index(){
        $data = \Modules\Products\Entities\Category::get();
        return response()->json($data);
    } 
    
    public function manage(Request $request){
        \Auth::user()->authorize('products_module_categories_manage');

        $data['activePage'] = ['categories' => 'categories'];
        $data['breadcrumb'] = [
            ['title' => 'Categories', 'url' => 'admin/categories/manage'],
        ];
        $data['addRecord'] = ['href' => route('categories.create')];
        if ($request->ajax()) {
            $data = \Modules\Products\Entities\Category::with('created_by_user', 'type_of_vendor','parent', 'children','status')->select('*');
            return Datatables::of($data)
            ->addColumn('action', function ($category) {
                $btn = '';
                $btn .= '<a data-action="edit" href=' .route('categories.edit', $category->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
                $btn .= '<a data-action="destroy" data-id='. $category->id. '  class="btn btn-xs red p-2  tooltips"><i class="fa fa-times" aria-hidden="true"></i> </a>';
                return $btn;
            })
            ->editColumn('image',function($row){
                return '<a href="'.$row->image_url.'" target="_blank"><img src="'.$row->image_url.'" width="50px" height="50px" style="border-radius: 10% !important;"></a>';
            })
            ->editColumn('status_id',function($row){
                $btn = view('core.status_label')->with(['row'=>$row])->render();
                return $btn;
            })
            ->addColumn('modalEn', function ($category) {
                $btn = '';
                $btn .= '<button type="button" class="btn btn-primary btn-sm"  data-toggle="modal" data-target="#descEn' . $category->id . '">';
                // $btn .= '<i class="fa-solid fa-eye"></i>';
                $btn .= "Show";
                $btn .= '</button>';
                $btn .= '<div class="modal fade" id="descEn'. $category->id. '" tabindex="-1" role="dialog" aria-labelledby="descEn'. $category->id.'" aria-hidden="true">';
                $btn .= '  <div class="modal-dialog" role="document">';
                $btn .= '<div class="modal-content">';
                $btn .= '<div class="modal-header">';
                $btn .= '<h5 class="modal-title" id="exampleModalLabel'. $category->id.'">Description</h5>';
                $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                $btn .= '<div class="modal-body">';
                $btn .=$category->getTranslations('description')['en'];
                $btn .= '</div>';
                $btn .= ' <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                $btn .= '</div></div></div></div>';
                return $btn;
            })
            ->addColumn('modalAr', function ($category) {
                $btn = '';
                $btn .= '<button type="button" class="btn btn-primary btn-sm"  data-toggle="modal" data-target="#descAr' . $category->id . '">';
                // $btn .= '<i class="fa-solid fa-eye"></i>';
                $btn .= "Show";

                $btn .= '</button>';
                $btn .= '<div class="modal fade" id="descAr'. $category->id. '" tabindex="-1" role="dialog" aria-labelledby="descAr'. $category->id.'" aria-hidden="true">';
                $btn .= '  <div class="modal-dialog" role="document">';
                $btn .= '<div class="modal-content">';
                $btn .= '<div class="modal-header">';
                $btn .= '<h5 class="modal-title" id="exampleModalLabel">Description</h5>';
                $btn .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $btn .= ' <span aria-hidden="true">&times;</span></button></div>';
                $btn .= '<div class="modal-body">';
                $btn .=$category->getTranslations('description')['ar'];
                $btn .= '</div>';
                $btn .= ' <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
                $btn .= '</div></div></div></div>';
                return $btn;
            })
            ->rawColumns(['action', 'modalEn' ,'modalAr','status_id'])
            ->filter(function ($query) use ($request) {
                if ($request->has('name') && $request->get('name') != null) {
                    $query->where('name->ar', 'like', "%{$request->get('name')}%")
                    ->orWhere('name->en', 'like', "%{$request->get('name')}%");
                }
                // if ($request->has('name') && $request->get('name') != null) {
                //     $query->where('name->en', 'like', "%{$request->get('name')}%");
                // }
                if ($request->has('type_id') && $request->get('type_id') != null) {
                    $query->whereHas('type_of_vendor', function($eloquent) use ($request){
                        $eloquent->whereId(trim($request->get('type_id')));
                    });
                }
                if ($request->has('status_id') && $request->get('status_id') != null) {
                    $query->where('status_id',$request->get('status_id'));
                }
                
            })
            ->toJson();
        }

        return view('products::categories',[
            'data' => $data,
            'types' => \Modules\Vendors\Entities\TypeOFVendor::get(),

        ]);
    }
    public function create(){
        \Auth::user()->authorize('products_module_categories_manage');

        $data['activePage'] = ['categories' => 'categories'];
        $data['breadcrumb'] = [
            ['title' => 'Categories', 'url' => 'admin/categories/manage'],
            ['title' => 'Add Category'],
        ];
        // $data['url'] = 'admin/categories';
        return view('products::create-category',[
            'data' => $data,
            'types' => \Modules\Vendors\Entities\TypeOFVendor::get(),
            'statuses' => \Modules\Products\Entities\CategoryStatus::get() 

        ]);
    }

    public function edit($id){
        \Auth::user()->authorize('products_module_categories_manage');

        $category = \Modules\Products\Entities\Category::with('created_by_user', 'type_of_vendor','parent', 'children')->whereId($id)->first();
        $data['activePage'] = ['categories' => 'categories'];
        $data['breadcrumb'] = [
            ['title' => 'Categories'],
            ['title' => 'Edit Category Info'],
        ];
        return view('products::edit-category',[
            'data' => $data,
            'category' => $category,
            'types' => \Modules\Vendors\Entities\TypeOFVendor::get(),
            'statuses' => \Modules\Products\Entities\ProductStatus::get()

        ]);
    }
    public function store(Request $request){
        \Auth::user()->authorize('products_module_categories_store');
        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'type_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $category = \Modules\Products\Entities\Category::where('name', $request->name_en)->first();
            if($category){
                return response()->json(['message' =>'This Data Are Already Exisit'],403);
            }
            $category = \Modules\Products\Entities\Category::where('name', $request->name_ar)->first();
            if($category){
                return response()->json(['message' =>'هذا التصنيف موجود مسبقا'],403);
            }
            $category = new \Modules\Products\Entities\Category;
            $category
            ->setTranslation('name', 'en', $request->name_en)
            ->setTranslation('name', 'ar',  $request->name_ar);

            $category
            ->setTranslation('description', 'en', $request->description_en)
            ->setTranslation('description', 'ar',  $request->description_ar);
            $category->vendor_type = $request->type_id;
            $category->status_id = '1';
            $category->created_by = \Auth::user()->id;
            $category->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok','data' => $category]);
        
    }
    public function update(Request $request, $id){
        \Auth::user()->authorize('products_module_categories_update');
        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'type_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $category = \Modules\Products\Entities\Category::where('id', '<>', $id)->where('name', $request->name_en)->first();
            if($category){
                return response()->json(['message' => 'This Category Are Already Exisit'],403);
            }
            $category = \Modules\Products\Entities\Category::where('id', '<>', $id)->where('name', $request->name_ar)->first();
            if($category){
                return response()->json(['message' => 'هذا التصنيف موجود مسبقا'],403);
            }
            $category =  \Modules\Products\Entities\Category::whereId($id)->first();
            $category
            ->setTranslation('name', 'en', $request->name_en)
            ->setTranslation('name', 'ar',  $request->name_ar);

            $category
            ->setTranslation('description', 'en', $request->description_en)
            ->setTranslation('description', 'ar',  $request->description_ar);
            $category->vendor_type = $request->type_id;
            $category->created_by = \Auth::user()->id;
            $category->save();
            \DB::commit();
        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(['message' => 'ok','data' => $category]);
        
    }

    public function show($id){
        $category = \Modules\Products\Entities\Category::whereId($id)->first();
        $images = $category->getMedia('category-image');
        $images_new  = collect([]);
        foreach($images as $image){
            $new['url'] =  url('/') . '/storage/app/public/' . $image->id . '/' . $image->file_name;
            $new['name'] = $image->file_name;
            $images_new->push($new);
        }
        return response()->json($images_new,200);
    }
    public function destroy(Request $request, $id){
        \Auth::user()->authorize('products_module_categories_destroy');
        $category = \Modules\Products\Entities\Category::whereId($id)->first();
        $category->delete();
        return response()->json('Ok',200);
    }
    public function addImage(Request $request){
        $id = $request->userId;

        $category = \Modules\Products\Entities\Category::whereId($id)->first();
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $extension = strtolower($request->file('file')->extension());
            $media_new_name = strtolower(md5(time())) . "." . $extension;
            $collection = "category-image";

            $category->addMediaFromRequest('file')
                ->usingFileName($media_new_name)
                ->usingName($request->file('file')->getClientOriginalName())
                ->toMediaCollection($collection);
            return response()->json(['message' => 'ok']);
        }
    }

    public function removeImage(Request $request, $id){
        $image = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('file_name', $id)->first();
        $image->delete();
        return response()->json(['message' => 'ok']);
    }
    
    public function vendorCategories($id){
       $data = \Modules\Products\Entities\Category::where('vendor_type', $id )->get();
       return response()->json($data);
    }
    
}
