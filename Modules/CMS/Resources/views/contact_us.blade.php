@extends('layouts.app')
@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" id="search-form" class="" role="form">

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label ><strong>{{__('User name')}}</strong></label>
                    <input type="text" class="form-control" name="name" >
                </div>
             
                <div class="form-group col-md-3">
                    <label ><strong>{{__('Email')}}</strong></label>
                    <input type="text" class="form-control" name="email" >
                </div>
                <div class="form-group col-md-3">
                    <label ><strong>{{__('Mobile Number')}}</strong></label>
                    <input type="text" class="form-control" name="mobile_no" >
                </div>
             
            </div>
        </form>
        
    </div>
</div>
   
<div class="content">
    <table class="table table-bordered data-table" style="width: 100%">
        <thead>
            <tr>
                <th>#</th>
                <th>{{__('User Name')}}</th>
                <th>{{__('Email')}}</th>
                <th>{{__('Mobile Number')}}</th>
                <th>{{__('Contnet')}}</th>
                <th>{{__('Reply Message')}}</th>
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
            url:  "{{ route('contact_us.manage') }}",
            data: function (d) {
                d.name = $('input[name="name"]').val()
                d.email = $('input[name="email"]').val()
                d.mobile_no = $('input[name="mobile_no"]').val()
            }

          },
          columns: [
            
              {data: 'id', name: 'id'},
              {data: 'name', name: 'name', defaultContent: "__"},
              {data: 'email', name: 'email', defaultContent: "__"},
              {data: 'mobile_no', name: 'mobile_no', defaultContent: "__"},
              {data: 'content', name: 'content', orderable: false, searchable: false, defaultContent: "__"},
              {data: 'reply', name: 'reply', orderable: false, searchable: false, defaultContent: "__"},
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
                        url: $("meta[name='BASE_URL']").attr("content") + '/admin/contact_us/' + $id,
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
