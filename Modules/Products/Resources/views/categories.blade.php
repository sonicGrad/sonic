@extends('layouts.app')
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
  
    <!--end::Subheader-->


    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid container">
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
                                            <label class="control-label">{{__('name')}}</label>
                                            <input type="text" value="{{request('name')?request('name'):''}}" class="form-control" name="name" placeholder="{{__('name')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">{{__('status')}}</label>
                                            <select id="multiple2" class="form-control"
                                                    name="status_id">
                                                <option value="">{{__('all')}}</option>
                                                <option value="1" {{request('status') == '1'?'selected':''}}>
                                                    {{__('active')}}
                                                </option>
                                                <option value="2" {{request('status') == '2'?'selected':''}}>
                                                    {{__('not_active')}}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
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
    <!--end::Entry-->
</div>
<div class="col1556">
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-checkable"  style="margin-top: 13px !important" id="kt_datatable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{__('Name Arabic')}}</th>
                    <th>{{__('Name English')}}</th>
                    <th>{{__('Description Arabic')}}</th>
                    <th>{{__('Description English')}}</th>
                    <th>{{__('Vendor Type')}}</th>
                    <th>{{__('created by')}}</th>
                    <th>{{__('Created At')}}</th>
                    <th>{{__('Status')}}</th>
                    <th width="100px">{{__('Actions')}}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>


@endsection
@section('js')
<script>
    $(function () {
        var table = $('#kt_datatable').DataTable({
          searching: false,
          destroy: true,
          processing: true,
          serverSide: true,
          autoWidth: false,
          dom: '<"dt-top-container"<B><"dt-center-in-div"l><f>r>t<"dt-filter-spacer"><ip>',
          ajax: {
            url:  "{{ route('categories.manage') }}",
            data: function (d) {
                d.name = $('input[name="name"]').val()
                d.status_id = $('select[name="status_id"]').val()
            }

          },
          columns: [
            {data: 'id', name: 'id'},
              {data: 'name.ar', name: 'name.ar', defaultContent: "__"},
              {data: 'name.en', name: 'name.en', defaultContent: "__"},
              {data: 'modalAr', name: 'modalAr', orderable: false, searchable: false, defaultContent: "__"},
              {data: 'modalEn', name: 'modalEn', orderable: false, searchable: false, defaultContent: "__"},
              {data: 'type_of_vendor.name.en', name: 'type_of_vendor.name.en', defaultContent: "__"},
              {data: 'created_by_user.full_name', name: 'created_by_user.full_name', defaultContent: "__"},
              {data: 'created_at', name: 'created_at', defaultContent: "__"},
              {data: 'status_id', name: 'status_id', defaultContent: "__"},
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
                        url: $("meta[name='BASE_URL']").attr("content") + '/admin/categories/' + $id,
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
