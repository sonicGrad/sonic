@extends('layouts.app')
@section('content')
<form id="target" action="{{route('roles.store')}}" method="post" class="form-horizontal" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name" >
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Description')}}</label>
        <div class="col-sm-10">
            
            <textarea  class="form-control " name="description" ></textarea>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{__('Vendors')}}</label>
        <div class="col-sm-10">
            <select name="vendor_id" id="vendor_id" class="form-control">
                <option value="">{{__("Choose Vendor...")}}</option>
                @foreach ($vendors as $vendor)
                <option value="{{ $vendor->id }}">{{ $vendor->company_name }}</option>
                 @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{__('Status')}}</label>
        <div class="col-sm-10">
            <select name="status" id="status" class="form-control">
                <option value="1">{{__('Pending')}}</option>
                <option value="2">{{__('Active')}}</option>
                <option value="3">{{__('Reject')}}</option>
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
   
    <div class="form-group">
        <label class="col-sm-2 control-label">{{__('Statring Date')}}</label>
        <div class="col-sm-10">
            <input required class="form-control" name="stating_date" id="stating_date"/>
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">{{__('Ended Date')}}</label>
        <div class="col-sm-10">
            <input required class="form-control" name="ended_date" id="ended_date"  />
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <input id="btn-submit" value="{{__('Add')}}" type="submit" class="btn btn-primary" >
        </div>
    </div>
</form>
@endsection
@section('js')
<script>$id = ''</script>
<script>
    myDropzone('ads')
</script>
<script>
    

    $("#btn-submit").on('click', function(event){
    event.preventDefault();
    var $this = $(this).closest('form');
    fail = true;
    http.checkRequiredFelids($this);
    if(!fail){
        return true;
    }
    var buttonText = $this.find('button:submit').text();
    var formdata=new FormData($this[0]);
    $this.find("button:submit").attr('disabled', true);
    $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');
        $.ajax({
            enctype: 'multipart/form-data',
            url :$("meta[name='BASE_URL']").attr("content") + "/admin/ads",
            data : formdata,
            contentType : false,
            processData : false,
            cache : false,
            dataType : 'json',
            type : 'post'
        })
        .done(function(response) {
            successfullyResponse(response)
         
        })
        .fail(function (response) {
            http.fail(response.responseJSON, true);
        });
});

setTimeout(() => {
    $("#stating_date").flatpickr();
    $("#ended_date").flatpickr();
}, 500);
</script>

@endsection