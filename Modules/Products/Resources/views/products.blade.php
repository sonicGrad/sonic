@extends('layouts.app')
@section('content')

<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container">
        <!--begin::Card-->
        <div class="gutter-b example example-compact">

            <div class="contentTabel">
                <button  type="button" class="btn btn-secondar btn--filter mr-2"><i class="icon-xl la la-sliders-h"></i>{{__('filter')}}</button>
                <div class="container box-filter-collapse" >
                    <div class="card" >
                        {{-- <form class="form-horizontal" method="get" action="{{url('/admin/categories')}}"> --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">{{__('Name Arabic')}}</label>
                                        <input type="text" value="" class="form-control" name="name_ar" placeholder="{{__('Name Arabic')}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">{{__('Name English')}}</label>
                                        <input type="text" value="" class="form-control" name="name_en" placeholder="{{__('Name English')}}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">{{__('status')}}</label>
                                        <select id="multiple2" class="form-control"name="status_id">
                                            <option value="">{{__('Choose Status...')}}</option>
                                            @foreach ($statues as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md">
                                    <div class="form-group">
                                        <label class="control-label">{{__('Admin Status')}}</label>
                                        <select id="admin_status" class="form-control"name="admin_status">
                                            <option value="">{{__('Choose Status...')}}</option>
                                            @foreach ($adminStatues as $adminStatus)
                                            <option value="{{ $adminStatus->id }}">{{ $adminStatus->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @if (\Auth::user()->hasRole('super_admin'))
                                <div class="col-md">
                                    <div class="form-group">
                                        <label class="control-label">{{__('Vendors Types')}}</label>
                                        <select id="type_id" class="form-control"name="type_id">
                                            <option value="">{{__('Choose Type...')}}</option>
                                            @foreach ($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div class="form-group">
                                        <label class="control-label">{{__('Categories')}}</label>
                                        <select id="category_id" class="form-control js-example-basic-single"name="category_id">
                                            <option value="">{{__('Choose Type...')}}</option>
                                            @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div class="form-group">
                                        <label class="control-label">{{__('Vendors')}}</label>
                                        <select id="vendor_id" class="form-control js-example-basic-single"name="vendor_id">
                                            <option value="">{{__('Choose Type...')}}</option>
                                            @foreach ($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endIf

                            </div>
                        {{-- </form> --}}
                    </div>
                </div>
            </div>
        </div>
        <!--end::Card-->
    </div>
    <!--end::Container-->
</div>
<div class="row" style="margin-bottom: 10px">
    <div class="col-sm-12 col-md-6">
        <div class="dt-buttons btn-group flex-wrap">
            <a  href="{{route('products.export_excel')}}" class="btn btn-secondary buttons-copy buttons-html5" tabindex="0" aria-controls="example1" type="button">
                <span>{{__('Export')}}</span>
            </a>
        </div>
        <div class="dt-buttons btn-group flex-wrap">
            <a href="{{route('products.import_excel')}}" class="btn btn-secondary buttons-copy buttons-html5" tabindex="0" aria-controls="example1" type="button">
                <span>{{__('Import')}}</span>
            </a>
        </div>
    </div>
</div>
<div class="content">
    <table class="table table-bordered data-table text-center" style="width: 100%">
        <thead>
            <tr>
                <th>#</th>
                <th>{{__('Name Arabic')}}</th>
                <th>{{__('Name English')}}</th>
                <th>{{__('Product Code')}}</th>
                <th>{{__('Description Arabic')}}</th>
                <th>{{__('Description English')}}</th>
                <th>{{__('Category name')}}</th>
                <th>{{__('Vendor name')}}</th>
                <th>{{__('Vendor Type')}}</th>
                <th>{{__('created by')}}</th>
                <th>{{__('Created At')}}</th>
                <th>{{__('Status')}}</th>
                <th>{{__('Admin Status')}}</th>
                <th width="100px">{{__('Actions')}}</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
@endsection
@section('js')
<script>
    $(function () {
        var table = $('.data-table').DataTable({
            searching: false,
          destroy: true,
          processing: true,
          serverSide: true,
          autoWidth: false,
          dom: '<"dt-top-container"<B><"dt-center-in-div"l><f>r>t<"dt-filter-spacer"><ip>',
          ajax: {
            url:  "{{ route('products.manage') }}",
            data: function (d) {
                d.name_ar = $('input[name="name_ar"]').val()
                d.name_en = $('input[name="name_en"]').val()
                d.description_en = $('input[name="description_en"]').val()
                d.description_ar = $('input[name="description_ar"]').val()
                d.type_id = $('select[name="type_id"]').val()
                d.vendor_id = $('select[name="vendor_id"]').val()
                d.category_id = $('select[name="category_id"]').val()
                d.status_id = $('select[name="status_id"]').val()
                d.admin_status = $('select[name="admin_status"]').val()
            }

          },
          columns: [

              {data: 'id', name: 'id'},
              {data: 'name.ar', name: 'name.ar', defaultContent: "__"},
              {data: 'name.en', name: 'name.en', defaultContent: "__"},
              {data: 'product_code', name: 'product_code', defaultContent: "__"},
              {data: 'modalAr', name: 'modalAr', orderable: false, searchable: false, defaultContent: "__"},
              {data: 'modalEn', name: 'modalEn', orderable: false, searchable: false, defaultContent: "__"},
              {data: 'category.name.en', name: 'category.name.en', defaultContent: "__"},
              {data: 'vendor.company_name', name: 'vendor.company_name', defaultContent: "__"},
              {data: 'category.type_of_vendor.name.en', name: 'category.type_of_vendor.name.en', defaultContent: "__"},
              {data: 'created_by_user.full_name', name: 'created_by_user.full_name', defaultContent: "__"},
              {data: 'created_at', name: 'created_at', defaultContent: "__"},
              {data: 'status_id', name: 'status_id', defaultContent: "__"},
              {data: 'admin_status', name: 'admin_status', defaultContent: "__"},
              {data: 'action', name: 'action', orderable: false, searchable: false, defaultContent: "__"},
          ],
      });

        $('input').on('change', function(e) {
            table.draw();
            e.preventDefault();
        });
        $('select').on('change', function(e) {
            table.draw();
            e.preventDefault();
        });
        changeStatusForAdmin('products');


        setTimeout(() => {
        $('a[data-action="destroy"]').on('click', function (e) {
            e.preventDefault();
            $id =$(this).attr("data-id");
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this imaginary file!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: $("meta[name='BASE_URL']").attr("content") + '/admin/products/' + $id,
                        type: 'DELETE',
                        data:{
                          _token: $("meta[name='csrf-token']").attr("content"),
                        }
                    })
                    .done(function(response) {
                        http.success({ 'message': response.message });
                        window.location.reload();
                    })
                    .fail(function(response){
                    http.fail(response.responseJSON, true);
                    })
                } else {
                    swal("Your imaginary file is safe!");
                }
                });
            });

        }, 1000);

    });
    setTimeout(() => {
        $('.js-example-basic-single').select2();
    }, 100);
  </script>
    <script>
     
        // setTimeout(() => {
        //    $('button[data-action="changeState"]').on('click', function (e) {  
        //        e.preventDefault();
        //        $this = $(this);
        //        setTimeout(() => {
              
        //         $('button[data-action="save-new-status"]').on('click', function (e) {  

        //             $value =  $this.closest('tr').find('select[name="admin_status"]').val()
        //            console.log($value);
        //            $id =$(this).attr("data-id");
        //            $.ajax({
        //                url: $("meta[name='BASE_URL']").attr("content") + '/admin/products/change-status/' + $id,
        //                type: 'POST',
        //                data:{
        //                    _token: $("meta[name='csrf-token']").attr("content"),
        //                    status: $value
        //                }
        //            })
        //            .done(function(response) {
        //                http.success({ 'message': response.message });
        //                window.location.reload();
        //            })
        //            .fail(function(response){
        //            http.fail(response.responseJSON, true);
        //            })
        //         });
        //     //        $('select[name="admin_status"]').on('change', function(e) {
        //     //        $value = $(this).val();
        //     //        console.log($value);
        //     //        e.preventDefault();
        //     //        $id =$(this).attr("data-id");
        //     //        $status = $()
                   
        //     //    });
        //        }, 1000);
        //     })
        // }, 1000);
     </script>
@endsection
