@extends('layouts.app')
@section('content')
<form id="target" action="{{route('roles.store')}}" method="post" class="form-horizontal" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name"  value="{{$ad->name}}">
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Description')}}</label>
        <div class="col-sm-10">
            
            <textarea  class="form-control " name="description" >{{$ad->description}}</textarea>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{__('Vendors')}}</label>
        <div class="col-sm-10">
            <select name="vendor_id" id="vendor_id" class="form-control ">
                <option value="">{{__("Choose Vendor...")}}</option>
                @foreach ($vendors as $vendor)
                <option value="{{ $vendor->id }}" @if ($vendor->id == $ad->vendor_id) selected @endif>{{ $vendor->company_name }}</option>
                 @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{__('Status')}}</label>
        <div class="col-sm-10">
            <select name="status" id="status"  class="form-control">
                <option value="1" @if($ad->status == '1') selected @endif>{{__('Pending')}}</option>
                <option value="2"  @if($ad->status == '2') selected @endif>{{__('Active')}}</option>
                <option value="3"  @if($ad->status == '3') selected @endif>{{__('Reject')}}</option>
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
   
    <div class="form-group">
        <label class="col-sm-2 control-label">{{__('Statring Date')}}</label>
        <div class="col-sm-10">
            <input required class="form-control" name="stating_date" id="stating_date"  value="{{$ad->stating_date}}"/>
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">{{__('Ended Date')}}</label>
        <div class="col-sm-10">
            <input required class="form-control" name="ended_date" id="ended_date"  value="{{$ad->ended_date}}" />
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
<script>$id = {{$ad->id}}</script>
<script>
    imageRemoveAndAppeared('ads', $id)
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
    data = {
        _token: $("meta[name='csrf-token']").attr("content"),
        name: $.trim($this.find("input[name='name']").val()),
        description: $.trim($this.find("textarea[name='description']").val()),
        stating_date: $.trim($this.find("input[name='stating_date']").val()),
        ended_date: $.trim($this.find("input[name='ended_date']").val()),
        status: $.trim($this.find("select[name='status']").val()),
        vendor_id : $.trim($this.find("select[name='vendor_id']").val()),
    }
    $this.find("button:submit").attr('disabled', true);
    $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');
    $.ajax({
        url: $("meta[name='BASE_URL']").attr("content") + '/admin/ads/' + $id,
        type: 'PUT',
        data:data
    })
    .done(function(response) {
        successfullyResponse(response)
    })
    .fail(function (response) {
        http.fail(response.responseJSON, true);
    })
    .always(function () {
        $this.find("button:submit").attr('disabled', false);
        $this.find("button:submit").html(buttonText);
    });
});

setTimeout(() => {
    $("#stating_date").flatpickr();
    $("#ended_date").flatpickr();
}, 500);
</script>

@endsection