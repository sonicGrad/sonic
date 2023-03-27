<?php

namespace Modules\Products\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\Datatables\Datatables;

class CategoryAttributeTypesController extends Controller{
    public function __construct(){
        $this->middleware(['auth']);
    } 
    public function manage(Request $request){
        \Auth::user()->authorize('products_module_category_attribute_types_manage');

        $data['activePage'] = ['categories' => 'category_attribute_types'];
        $data['breadcrumb'] = [
            ['title' => 'Category Attribute Types'],
        ];
        $data['addRecord'] = ['href' => route('category_attribute_types.create')];
        if ($request->ajax()) {
            $data = \Modules\Products\Entities\CategoryAttributeType::with('category','attribute')->select('*');
            return Datatables::of($data)
            ->addColumn('action', function ($category) {
                $btn = '';
                $btn .= '<a data-action="edit" href=' .route('category_attribute_types.edit', $category->id) . '  class="btn btn-sm btn-clean btn-icon edit_item_btn"><i class="la la-edit"></i> </a>';
                $btn .= '<a data-action="destroy" data-id='. $category->id. '  class="btn btn-xs red p-2  tooltips"><i class="fa fa-times" aria-hidden="true"></i> </a>';
                return $btn;
            })
            ->addColumn('category_name', function ($data) {
                return $data->category->getTranslations('name')['ar'].' [ '.$data->category->getTranslations('name')['en'] . ']';
            })
            ->addColumn('attribute_name', function ($data) {
               return  $data->attribute->getTranslations('name')['ar'].' [ '.$data->attribute->getTranslations('name')['en'] . ']';
            })
            ->rawColumns(['action','category_name','attribute_name'])
            ->filter(function ($query) use ($request) {
                if ($request->has('category_name') && $request->get('category_name') != null) {
                    $query->whereHas('category', function($q)use($request){
                       $q->where('name->ar', 'like', "%{$request->get('category_name')}%");
                    //    ->orWhere('name->en', 'like', "%{$request->get('category_name')}%");
                    });
                }
                if ($request->has('attribute_name') && $request->get('attribute_name') != null) {
                    $query->whereHas('category', function($q)use($request){
                        $q->where('name->ar', 'like', "%{$request->get('attribute_name')}%");
                        // ->orWhere('name->en', 'like', "%{$request->get('attribute_name')}%")
                     });
                }
            })
            ->toJson();
        }

        return view('products::category_attribute_types',[
            'data' => $data,
        ]);
    }

    public function create(){
        \Auth::user()->authorize('products_module_category_attribute_types_manage');

        $data['activePage'] = ['categories' => 'category_attribute_types'];
        $data['breadcrumb'] = [
            ['title' => 'Create Category Attribute Types'],
        ];

        return view('products::create_category_attribute_types',[
            'data' => $data,
            'types' => \Modules\Vendors\Entities\TypeOFVendor::get()
        ]);
    }

    public function store(Request $request){
        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'type_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $attribute =new \Modules\Products\Entities\AttributeType;
            $attribute
            ->setTranslation('name', 'en', $request->name_en)
            ->setTranslation('name', 'ar',  $request->name_ar);
            $attribute->save();
            if(count($request->type_id) == 0){
                return response()->json(['message' => 'Not Allow Please add Categories'], 403);
            }
            foreach ($request->type_id as $type) {
                $attribute_category = new \Modules\Products\Entities\CategoryAttributeType;
                $attribute_category->attribute_type_id = $attribute->id;
                $attribute_category->category_id = $type;
                $attribute_category->save();
            }
            if($request->list_name && count($request->list_name) != 0){
                foreach ($request->list_name as $list_name) {
                    $attribute_list =new \Modules\Products\Entities\AttributeTypeValue;
                    $attribute_list->attribute_type_id = $attribute->id;
                    $attribute_list->name = $list_name;
                    $attribute_list->save();
                }
            }

            \DB::commit();

        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }
        return response()->json(['message' => 'ok', 'data'=> $attribute]);
    }

    public function edit($id){
        \Auth::user()->authorize('products_module_category_attribute_types_manage');

        $data['activePage'] = ['categories' => 'category_attribute_types'];
        $data['breadcrumb'] = [
            ['title' => 'Edit Category Attribute Types'],
        ];
        $list = \Modules\Products\Entities\AttributeTypeValue::where('attribute_type_id', $id)->pluck('name');
        $ids = \Modules\Products\Entities\CategoryAttributeType::where('attribute_type_id', $id)->pluck('category_id');
        // return count($list->toArray()) == 0 ? '' : $list;
        return view('products::edit_category_attribute_types',[
            'data' => $data,
            'type' => \Modules\Products\Entities\AttributeType::whereId($id)->first(),
            'types' => \Modules\Vendors\Entities\TypeOFVendor::get(),
            'list' => count($list->toArray()) == 0 ? '' : $list ,
            'ids' => $ids->toArray(),
        ]);
    }

    public function update(Request $request, $id){
        \Auth::user()->authorize('products_module_category_attribute_types_update');

        $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'type_id' => 'required',
        ]);
        \DB::beginTransaction();
        try {
            $attribute = \Modules\Products\Entities\AttributeType::whereId($id)->first();
            $attribute
            ->setTranslation('name', 'en', $request->name_en)
            ->setTranslation('name', 'ar',  $request->name_ar);
            $attribute->save();
            if(count($request->type_id) == 0){
                return response()->json(['message' => 'Not Allow Please add Categories'], 403);
            }
            \Modules\Products\Entities\CategoryAttributeType::where('attribute_type_id', $id)->delete();
            foreach ($request->type_id as $type) {
                $attribute_category = new \Modules\Products\Entities\CategoryAttributeType;
                $attribute_category->attribute_type_id = $attribute->id;
                $attribute_category->category_id = $type;
                $attribute_category->save();
            }
            if(count($request->list_name) != 0){
                \Modules\Products\Entities\AttributeTypeValue::where('attribute_type_id', $id)->delete();
                foreach ($request->list_name as $list_name) {
                    $attribute_list =new \Modules\Products\Entities\AttributeTypeValue;
                    $attribute_list->attribute_type_id = $attribute->id;
                    $attribute_list->name = $list_name;
                    $attribute_list->save();
                }
            }

            \DB::commit();

        } catch (\Exception $e) {

            \DB::rollback();
            return response()->json(['message' => $e->getMessage()], 403);
        }
            return response()->json(['message' => 'ok', 'data'=> $attribute]);
    }

    public function show($id){
        // \Auth::user()->authorize('products_module_category_attribute_types_manage');

        return $list = \Modules\Products\Entities\AttributeTypeValue::where('attribute_type_id', $id)->pluck('name');
    }
    public function destroy(Request $request, $id){
        \Auth::user()->authorize('products_module_category_attribute_types_destroy');

        \Modules\Products\Entities\AttributeType::destroy($id);
        return response()->json('Ok',200);
    }
    public function category($id){
        $attribute = \Modules\Products\Entities\CategoryAttributeType::where('attribute_type_id', '<>', '1')->with('attribute')->where('category_id', $id)->get();
        return response()->json($attribute);
    }
}


