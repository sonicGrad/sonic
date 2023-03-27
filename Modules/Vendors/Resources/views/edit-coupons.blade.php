@extends('layouts.app')
@section('content')
<form id="target" method="post" class="form-horizontal">
    @csrf
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Code')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control" name="code" value="{{$coupon->code}}" >
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name Arabic')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name_ar" value="{{$coupon->getTranslations('name')['ar']}}" >
             <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name English')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name_en" value="{{$coupon->getTranslations('name')['en']}}" >
             <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Descritpion Arabic')}}</label>
        <div class="col-sm-10">
            
            <textarea required type="text" class="form-control " name="description_ar" >{{$coupon->getTranslations('description')['ar']}}</textarea>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Descritpion English')}}</label>
        <div class="col-sm-10">
            
            <textarea required type="text" class="form-control " name="description_en" >{{$coupon->getTranslations('description')['en']}}</textarea>
            <p class="invalid-feedback"></p>
        </div>
        
    </div>
    
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{__('Vendors')}}</label>
        <div class="col-sm-10">
            <select name="vendor_id" id="vendor_id" class="form-control ">
                <option value="">{{__("Choose Vendor...")}}</option>
                @foreach ($vendors as $vendor)
                <option value="{{ $vendor->id }}" @if ($vendor->id == $coupon->vendor_id) selected @endif >{{ $vendor->company_name }}</option>
                 @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{__('Status')}}</label>
        <div class="col-sm-10">
            <select name="status" id="status" class="form-control ">
                <option value="1" @if($coupon->status == '1') selected @endif>{{__('Active')}}</option>
                <option value="2" @if($coupon->status == '2') selected @endif>{{__('Reject')}}</option>
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{__('Type')}}</label>
        <div class="col-sm-10">
            <select required name="type_id" id="type_id" class="form-control">
                <option value="">{{__("Choose Type...")}}</option>
                
                @foreach ($types as $type)
                <option value="{{ $type->id }}" @if ($type->id == $coupon->type_id) selected @endif >{{ $type->name }}</option>
            @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Value')}}</label>
        <div class="col-sm-10">
            
            <input  type="text" class="form-control " name="value" value="{{ $coupon->value }}">
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Amount')}}</label>
        <div class="col-sm-10">
            
            <input  type="text" class="form-control " name="amount"  value="{{ $coupon->amount }}">
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">{{__('Starting Date')}}</label>
        <div class="col-sm-10">
            <input required class="form-control" name="starting_data" id="starting_data"  value="{{ $coupon->starting_data }}" />
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">{{__('Ended Date')}}</label>
        <div class="col-sm-10">
            <input  class="form-control" name="ended_data" id="ended_data" value="{{ $coupon->ended_data }}" required />
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
<script>$id = {{$coupon->id}}</script>

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
        code: $.trim($this.find("input[name='code']").val()),
        name_en: $.trim($this.find("input[name='name_en']").val()),
        name_ar: $.trim($this.find("input[name='name_ar']").val()),
        value: $this.find("input[name='value']").val(),
        amount: $this.find("input[name='amount']").val(),
        description_en: $this.find("textarea[name='description_en']").val(),
        description_ar: $this.find("textarea[name='description_ar']").val(),
        starting_data: $this.find("input[name='starting_data']").val(),
        ended_data: $this.find("input[name='ended_data']").val(),
        status: $this.find("select[name='status']").val(),
        type_id: $this.find("select[name='type_id']").val(),
        vendor_id: $this.find("select[name='vendor_id']").val(),
    }
    $this.find("button:submit").attr('disabled', true);
    $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');
    $.ajax({
        url: $("meta[name='BASE_URL']").attr("content") + '/admin/coupons/' + $id,
        type: 'PUT',
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

setTimeout(() => {
    $("#starting_data").flatpickr();
    $("#ended_data").flatpickr();
}, 500);
</script>

@endsection