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
                                <div class="col-md">
                                    <div class="form-group">
                                        <label class="control-label">{{__('Full Name')}}</label>
                                        <input type="text" value="" class="form-control" name="full_name" placeholder="{{__('Full Name')}}">
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div class="form-group">
                                        <label class="control-label">{{__('National Number')}}</label>
                                        <input type="text" value="" class="form-control" name="national_id" placeholder="{{__('National Number')}}">
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div class="form-group">
                                        <label class="control-label">{{__('Mobile Number')}}</label>
                                        <input type="text" value="" class="form-control" name="mobile_no" placeholder="{{__('Mobile Number')}}">
                                    </div>
                                </div>
                                <div class="col-md">
                                    <div class="form-group">
                                        <label class="control-label">{{__('Email')}}</label>
                                        <input type="text" value="" class="form-control" name="email" placeholder="{{__('Email')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md">
                                    <div class="form-group">
                                        <label class="control-label">{{__('Created At')}}</label>
                                        <input type="text" value="" class="form-control" name="created_at" placeholder="{{__('Created At')}}" id="created_at">
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
                                        <label class="control-label">{{__('Type Of User')}}</label>
                                        <select id="role_name" class="form-control js-example-basic-single"name="role_name">
                                            <option value="">{{__('Choose Type...')}}</option>
                                            @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
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
                <th>{{__('National Number')}}</th>
                <th>{{__('Full Name')}}</th>
                <th>{{__('Province')}} </th>
                <th>{{__('Address')}}</th>
                <th>{{__('Mobile Number')}}</th>
                <th>{{__('Email')}} </th>
                <th>{{__('Type Of User')}}</th>
                <th>{{__('created by')}}</th>
                <th>{{__('Created At')}}</th>
                <th>{{__('Status')}}</th>
                <th width="100px">{{__('Action')}}</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
@endsection
@section('js')
<script>$vendor =" {{\Auth::user()->hasRole('vendor')}}"</script>
<script>
    
        $(function () {
          var table = $('.data-table').DataTable({
            searching: false,
              processing: true,
              serverSide: true,
              dom: '<"dt-top-container"<B><"dt-center-in-div"l><f>r>t<"dt-filter-spacer"><ip>',

              ajax: {
                url:  "{{ route('users.manage') }}",
                data: function (d) {
                    d.mobile_no = $('input[name="mobile_no"]').val(),
                    d.email = $('input[name="email"]').val(),
                    d.full_name = $('input[name="full_name"]').val(),
                    d.national_id = $('input[name="national_id"]').val(),
                    d.province_id  = $('select[name="province_id"]').val(),
                    d.role_name  = $('select[name="role_name"]').val(),
                    d.search = $('input[type="search"]').val(),
                    d.created_at = $('input[name="created_at"]').val()
                }
    
              },
              columns: [
                
                  {data: 'id', name: 'id', defaultContent: "__"},
                  {data: 'national_id', name: 'national_id', defaultContent: "__"},
                  {data: 'full_name', name: 'full_name', defaultContent: "__"},
                  {data: 'province.name.en', name: 'province_id', defaultContent: "__"},
                  {data: 'address', name: 'address', defaultContent: "__"},
                  {data: 'mobile_no', name: 'mobile_no', defaultContent: "__"},
                  {data: 'email', name: 'email', defaultContent: "__"},
                  {data: 'roles', name: 'roles', orderable: false, searchable: false, defaultContent: "__"},
                  {data: 'created_by.full_name' ?? '-', name: 'created_by', defaultContent: "__"},
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
                            url: $("meta[name='BASE_URL']").attr("content") + '/admin/users/' + $id,
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
            console.log($('a[data-action="change-pass"]'));
                $('a[data-action="change-pass"]').on('click', function (e) {  
                    e.preventDefault();
    
                    console.log('object'); 
                    $id = $(this).attr('data-id');
                    swal({
                        title: "Are you sure Change Password?",
                        text: "That Will change password for user and send msg for him",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                        })
                        .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: $("meta[name='BASE_URL']").attr("content") + '/admin/users/change-password-for-users/' + $id,
                                type: 'post',
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
            $("#created_at").flatpickr({
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                mode: "range"
            });
        });
  </script>
@endsection
