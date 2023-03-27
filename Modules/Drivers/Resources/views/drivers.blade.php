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
                                        <label class="control-label">{{__('Driver License Number')}}</label>
                                        <input type="text" value="" class="form-control" name="driving_license_no" placeholder="{{__('Driver License Number')}}">
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div class="form-group">
                                        <label class="control-label">{{__('Province')}}</label>
                                        <select id="province_id" class="form-control js-example-basic-single"name="province_id">
                                            <option value="">{{__('Choose Type...')}}</option>
                                            @foreach ($provinces as $province)
                                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div class="form-group">
                                        <label class="control-label">{{__('drivers Types')}}</label>
                                        <select id="type_id" class="form-control js-example-basic-single"name="type_id">
                                            <option value="">{{__('Choose Type...')}}</option>
                                            @foreach ($types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
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

   
<div class="content">
    <table class="table table-bordered data-table text-center" style="width: 100%">
        <thead>
            <tr>
                <th>#</th>
                <th>{{__('Name')}}</th>
                <th>{{__('Type')}}</th>
                <th>{{__('Province')}}</th>
                <th>{{__('Address')}}</th>
                <th>{{__('Driving  License Number')}}</th>
                <th>{{__('Driving  License Ended Date')}}</th>
                <th>{{__('Created by')}}</th>
                <th>{{__('Created At')}}</th>
                <th>{{__('Status')}}</th>
                <th width="100px">{{__('Actions')}}</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
@endsection
@section('js')
<script>$lang = "{{app()->getLocale()}}"</script>

<script>
    $(function () {
        var table = $('.data-table').DataTable({
        searching: false,
          processing: true,
          serverSide: true,
          dom: '<"dt-top-container"<B><"dt-center-in-div"l><f>r>t<"dt-filter-spacer"><ip>',

          ajax: {
            url:  "{{ route('drivers.manage') }}",
            data: function (d) {
                d.company_name = $('input[name="company_name"]').val()
                d.province_id = $('select[name="province_id"]').val()
                d.type_id = $('select[name="type_id"]').val()
            }

          },
          columns: [
              {data: 'id', name: 'id', defaultContent: "__"},
              {data: 'user.full_name', name: 'user.full_name', defaultContent: "__"},
              {data: 'type_of_driver.name.en', name: 'type_of_driver.name.en', defaultContent: "__"},
              {data: 'user.province.name.en', name: 'user.province.name', defaultContent: "__"},
              {data: 'user.address', name: 'user.address', defaultContent: "__"},
              {data: 'driving_license_no', name: 'driving_license_no', defaultContent: "__"},
              {data: 'driving_license_ended', name: 'driving_license_ended', defaultContent: "__"},
              {data: 'created_by_user.full_name', name: 'created_by_user.full_name', defaultContent: "__"},
              {data: 'created_at', name: 'created_at', defaultContent: "__"},
              {data: 'status.name.en', name: 'status.name.en', defaultContent: "__"},
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
                        url: $("meta[name='BASE_URL']").attr("content") + '/admin/drivers/' + $id,
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
