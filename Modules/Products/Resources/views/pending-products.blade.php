@extends('layouts.app')
@section('content')
@if (\Auth::user()->hasRole('super_admin'))

<div class="card">
    <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label ><strong>{{__('Name Arabic')}}</strong></label>
                    <input type="text" class="form-control" name="name_ar" >
                </div>
                <div class="form-group col-md-3">
                    <label ><strong>{{__('Name English')}}</strong></label>
                    <input type="text" class="form-control" name="name_en" >
                </div>

                <div class="form-group col-md-3">
                    <label ><strong>{{__('Vendors Types')}}</strong></label>
                    <select id='type_id' class="form-control" name="type_id" style="width: 200px">
                        <option value="">{{__('Choose Type...')}}</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="form-row row">
                <div class="form-group col-md-3">
                    <label ><strong>{{__('Categories')}}</strong></label>
                    <select id='category_id' class="form-control js-example-basic-single" name="category_id" style="width: 200px">
                        <option value="">{{__('Choose Type...')}}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3" style="margin-top: 25px">
                    <label ><strong>{{__('Vendors')}}</strong></label>
                    <select id='vendor_id' class="form-control js-example-basic-single" name="vendor_id" style="width: 200px">
                        <option value="">{{__('Choose Type...')}}</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->company_name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
    </div>
</div>
@endIf
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
          processing: true,
          serverSide: true,
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
              {data: 'status.name.en', name: 'status.name.en', defaultContent: "__"},
              {data: 'admin_status.name.en', name: 'admin_status.name.en', defaultContent: "__"},
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
       


    });
    setTimeout(() => {
        $('.js-example-basic-single').select2();
    }, 100);
   
  </script>
  <script>
     setTimeout(() => {
        $('button[data-action="changeState"]').on('click', function (e) {  
            e.preventDefault();
            setTimeout(() => {
                $('select[name="admin_status"]').on('change', function(e) {
                $value = $(this).val();
                console.log($value);
                e.preventDefault();
                $id =$(this).attr("data-id");
                $status = $()
                $.ajax({
                    url: $("meta[name='BASE_URL']").attr("content") + '/admin/change-status/' + $id,
                    type: 'POST',
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
            });
            }, 1000);
        })
        }, 1000);
  </script>
@endsection
