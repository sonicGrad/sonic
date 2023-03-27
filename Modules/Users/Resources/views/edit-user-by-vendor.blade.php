@extends('layouts.app')
@section('content')
<form id="target" action="{{route('users.store')}}" method="post" class="form-horizontal">
    @csrf
    <div class="form-group">
        <label for="" class="col-sm-2 control-label" style="width:bold;font-size:18px">{{__('User Information')}}</label>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('First Name')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="first_name"  value="{{ $user->first_name }}">
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Last Name')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="last_name" value="{{ $user->last_name }}">
            <p class="invalid-feedback"></p>
        </div>
        
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('National Number | Passport Number')}}</label>
        <div class="col-sm-10">

            <input type="text" class="form-control " name="national_id" value="{{ $user->national_id }}">
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Mobile Number')}}</label>
        <div class="col-sm-10">

            <input required type="text" class="form-control" name="mobile_no"  value="{{ $user->mobile_no}}">
            <p class="invalid-feedback"></p>
        </div>

    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Email')}}</label>
        <div class="col-sm-10">

            <input required type="text" class="form-control " name="email"  value="{{ $user->email }}">
            <p class="invalid-feedback"></p>
        </div>

    </div>
    
    <div class="form-group">
        <label for="" class="col-sm-2 control-label" style="width:bold;font-size:18px">{{__('Sub Vendor Information')}}</label>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Company Name')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="company_name"  value="{{ $vendor->company_name }}">
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Province') }}</label>
        <div class="col-sm-10">
            <select required name="province_id" id="province_id" class="form-control ">
                <option value="">{{__("Choose Province...")}}</option>
                @foreach ($provinces as $province)
                
                <option value="{{ $province->id }}" @if($province->id  == $user->province_id ) selected  @endif>{{ $province->name }}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Address')}}</label>
        <div class="col-sm-10">

            <input required type="text" class="form-control " name="address" placeholder="{{__('qaiat, dohad, ...')}}"   value="{{ $user->address}}">
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Maximum Distance')}}</label>
        <div class="col-sm-10">

            <input required type="text" class="form-control " name="maximum_distance" placeholder="5,7,10.5,..."   value="{{ $vendor->maximum_distance}}">
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">{{__('Open At')}}</label>
        <div class="col-sm-10">
            <input required class="form-control" name="starting_time" id="starting_time" value="{{$vendor->starting_time}}" />
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">{{__('Close At')}}</label>
        <div class="col-sm-10">
            <input required class="form-control" name="closing_time" id="closing_time" value="{{$vendor->closing_time}}" />
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Vendor Status') }}</label>
        <div class="col-sm-10">
            <select name="status_id" id="status_id" class="form-control ">
                <option value="">{{__("Choose Status...")}}</option>
                @foreach ($statuses as $status)

                <option value="{{ $status->id }}" @if($status->id  == $vendor->status_id ) selected  @endif>{{ $status->name }}</option>
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
<script>$id = {{$user->id}}</script>

<script>$lang = "{{app()->getLocale()}}"</script>
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
            national_id: $.trim($this.find("input[name='national_id']").val()),
            first_name: $this.find("input[name='first_name']").val(),
            last_name: $this.find("input[name='last_name']").val(),
            mobile_no: $.trim($this.find("input[name='mobile_no']").val()),
            email: $.trim($this.find("input[name='email']").val()),
            address: $this.find("input[name='address']").val(),
            province_id: $this.find("select[name='province_id']").val(),
            company_name: $this.find("input[name='company_name']").val(),
            status_id: $this.find("select[name='status_id']").val(),
            starting_time: $this.find("input[name='starting_time']").val(),
            closing_time: $this.find("input[name='closing_time']").val(),
            maximum_distance: $this.find("input[name='maximum_distance']").val(),
        }
        $this.find("button:submit").attr('disabled', true);
        $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');

        $.ajax({
            url: $("meta[name='BASE_URL']").attr("content") + '/admin/users/update-for-vendor/' + $id,
            type: 'POST',
            data:data
        })
        .done(function(response) {
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