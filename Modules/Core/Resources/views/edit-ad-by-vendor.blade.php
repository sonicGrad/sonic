@extends('layouts.app')
@section('content')
<form id="target" action="" method="post" class="form-horizontal" enctype="multipart/form-data">
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
        <label class="col-sm-2 control-label">{{__('Statring Date')}}</label>
        <div class="col-sm-10">
            <input required class="form-control" name="stating_date" id="stating_date" value="{{$ad->stating_date}}"/>
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">{{__('Ended Date')}}</label>
        <div class="col-sm-10">
            <input required class="form-control" name="ended_date" id="ended_date" value="{{$ad->ended_date}}" />
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
    myDropzone('ads')
    imageRemoveAndAppeared('ads', $id)

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
            url :$("meta[name='BASE_URL']").attr("content") + "/admin/ads/update-for-vendor/" + $id,
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