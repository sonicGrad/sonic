@extends('layouts.app')
@section('content')
<form id="target" action="{{route('roles.store')}}" method="post" class="form-horizontal">
    @csrf
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name Arabic')}}</label>
        <div class="col-sm-10">
            
            <input type="text" required class="form-control " name="name_ar"  value="{{$county_province->getTranslations('name')['ar'] }}">
            
                    <p class="invalid-feedback">{{ $message }}</p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name English')}}</label>
        <div class="col-sm-10">
            
            <input type="text" required class="form-control " name="name_en"  value="{{ $county_province->getTranslations('name')['en']}}">
            <p class="invalid-feedback">{{ $message }}</p>
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
<script>$id = {{$county_province->id}}</script>

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
        name_en: $.trim($this.find("input[name='name_en']").val()),
        name_ar: $this.find("input[name='name_ar']").val(),
    }
    $this.find("button:submit").attr('disabled', true);
    $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');

    $.ajax({
        url: $("meta[name='BASE_URL']").attr("content") + '/admin/county_province/' + $id,
        type: 'PUT',
        data:data
    })
    .done(function(response) {
        http.success({ 'message': response.message });
        window.location.reload();
    })
    .fail(function (response) {
        http.fail(response.responseJSON, true);
    });
});


</script>

@endsection