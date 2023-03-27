@extends('layouts.app')
@section('content')
<form id="target" action="{{route('roles.store')}}" method="post" class="form-horizontal">
    @csrf
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name Arabic')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="label" >
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name English')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name" >
            <p class="invalid-feedback"></p>
        </div>
        
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Parent Role') }}</label>
        <div class="col-sm-10">
            <select  name="parent_id" id="parent_id" class="form-control">
                <option value="">{{__("Choose Type...")}}</option>
                @foreach ($parentRoles as $role)
                    <option value="{{$role->id}}">{{$role->name}}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
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
        label: $this.find("input[name='label']").val(),
        parent_id: $this.find("select[name='parent_id']").val(),
    }
    $this.find("button:submit").attr('disabled', true);
    $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');

    $.post($("meta[name='BASE_URL']").attr("content") + "/admin/roles", data,
    function (response, status) {
        http.success({ 'message': response.message });
        window.location.reload();
    })
    .fail(function (response) {
        http.fail(response.responseJSON, true);
    })
    .always(function () {
        $this.find("button:submit").attr('disabled', false);
        $this.find("button:submit").html(buttonText);
    });
});


</script>

@endsection