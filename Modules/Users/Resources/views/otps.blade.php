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
                                        <label class="control-label">{{__('Mobile Number')}}</label>
                                        <input type="text" value="" class="form-control" name="mobile_no" placeholder="{{__('Mobile Number')}}">
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
                <th>{{__('Mobile Number')}}</th>
                <th>{{__('Message')}}</th>
                <th>{{__('Created At')}}</th>
                {{-- <th width="100px">{{__('Action')}}</th> --}}
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
            url:  "{{ route('otps.manage') }}",
            data: function (d) {
                d.mobile_no = $('input[name="mobile_no"]').val()
            }

          },
          columns: [
            
              {data: 'id', name: 'id', defaultContent: "__"},
              {data: 'mobile_no', name: 'mobile_no', defaultContent: "__"},
              {data: 'message', name: 'message', defaultContent: "__"},
              {data: 'created_at', name: 'created_at', defaultContent: "__"},
            //   {data: 'action', name: 'action', orderable: false, searchable: false, defaultContent: "__"},
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
                        url: $("meta[name='BASE_URL']").attr("content") + '/admin/otps/' + $id,
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
