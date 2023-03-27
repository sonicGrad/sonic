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
                        <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{__('Code')}}</label>
                                <input type="text" value="" class="form-control" name="code" placeholder="{{__('Code')}}">
                            </div>
                        </div>
                            @if (\Auth::user()->hasRole('super_admin'))
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
                            <div class="col-md">
                                <div class="form-group">
                                    <label class="control-label">{{__('Status')}}</label>
                                    <select id="status_id" class="form-control js-example-basic-single"name="status_id">
                                        <option></option>
                                        <option value="1">{{__('Active')}}</option>
                                        <option value="2">{{__('Reject')}}</option>
                                    </select>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Card-->
    </div>
    <!--end::Container-->
</div>
   
<div class="content">
    <table class="table table-bordered data-table" style="width: 100%">
        <thead>
            <tr>
                <th>#</th>
                <th>{{__('Code')}}</th>
                <th>{{__('Vendor Name')}}</th>
                <th>{{__('Name Arabic')}}</th>
                <th>{{__('Name English')}}</th>
                <th>{{__('Description Arabic')}}</th>
                <th>{{__('Description English')}}</th>
                <th>{{__('Value')}}</th>
                <th>{{__('Amount (Condition)')}}</th>
                <th>{{__('Type')}}</th>
                <th>{{__('Starting Data')}}</th>
                <th>{{__('Endned Data')}}</th>
                <th>{{__('Status')}}</th>
                <th>{{__('Admin Status')}}</th>
                <th>{{__('Created By')}}</th>
                <th>{{__('Created At')}}</th>
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
          dom: '<"dt-top-container"<B><"dt-center-in-div"l><f>r>t<"dt-filter-spacer"><ip>',

          ajax: {
            url:  "{{ route('coupons.manage') }}",
            data: function (d) {
                d.code = $('input[name="code"]').val()
                d.type_id = $('select[name="type_id"]').val()
                d.vendor_id = $('select[name="vendor_id"]').val()
            }

          },
          columns: [
            
              {data: 'id', name: 'id', defaultContent: "__"},
              {data: 'code', name: 'code', defaultContent: "__"},
              {data: 'vendor.company_name', name: 'vendor.company_name', defaultContent: "__"},
              {data: 'name.ar', name: 'name.ar', defaultContent: "__"},
              {data: 'name.en', name: 'name.en', defaultContent: "__"},
              {data: 'modalAr', name: 'modalAr', orderable: false, searchable: false, defaultContent: "__"},
              {data: 'modalEn', name: 'modalEn', orderable: false, searchable: false, defaultContent: "__"},
              {data: 'value', name: 'value', defaultContent: "__"},
              {data: 'amount', name: 'amount', defaultContent: "__"},
              {data: 'type.name.en', name: 'type.name.en', defaultContent: "__"},
              {data: 'starting_data', name: 'starting_data', defaultContent: "__"},
              {data: 'ended_data', name: 'ended_data', defaultContent: "__"},
              {data: 'status', name: 'status', defaultContent: "__"},
              {data: 'admin_status.name.en', name: 'admin_status.name.en', orderable: false, searchable: false , defaultContent: "__"},
              {data: 'created_by_user.full_name', name: 'created_by_user.full_name' , defaultContent: "__"},
              {data: 'created_at', name: 'created_at'},
              {data: 'action', name: 'action', orderable: false, searchable: false},
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
        changeStatusForAdmin('coupons');
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
                        url: $("meta[name='BASE_URL']").attr("content") + '/admin/coupons/' + $id,
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
  </script>
@endsection
