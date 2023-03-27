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
    <table class="table table-bordered data-table" style="width: 100%">
        <thead>
            <tr>
                <th>#</th>
                <th>{{__('Name Arabic')}}</th>
                <th>{{__('Name English')}}</th>
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
          ajax: {
            url:  "{{ route('county_province.manage') }}",
            data: function (d) {
                d.name_en = $('input[name="name_en"]').val()
                d.name_ar = $('input[name="name_ar"]').val()
            }

          },
          columns: [
            
              {data: 'id', name: 'id'},
              {data: 'name.ar', name: 'name.ar'},
              {data: 'name.en', name: 'name.en'},
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
                        url: $("meta[name='BASE_URL']").attr("content") + '/admin/county_province/' + $id,
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
