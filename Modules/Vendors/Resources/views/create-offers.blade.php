@extends('layouts.app')
@section('content')
<form id="target" method="post" class="form-horizontal">
    @csrf
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name Arabic')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name_ar" >
            <p class="invalid-feedback"></p>
            
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name English')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name_en" >
            <p class="invalid-feedback"></p>
            
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Descritpion Arabic')}}</label>
        <div class="col-sm-10">
            
            <textarea required type="text" class="form-control " name="description_ar" ></textarea>
            <p class="invalid-feedback"></p>
        </div>
        
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Descritpion English')}}</label>
        <div class="col-sm-10">
            
            <textarea required type="text" class="form-control " name="description_en" ></textarea>
            <p class="invalid-feedback"></p>
        </div>
        
    </div>
    
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{__('Vendors')}}</label>
        <div class="col-sm-10">
            <select required name="vendor_id" id="vendor_id" class="form-control ">
                <option value="">{{__("Choose Vendor...")}}</option>
                @foreach ($vendors as $vendor)
                <option value="{{ $vendor->id }}">{{ $vendor->company_name }}</option>
                 @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{__('Products Under Offer')}}</label>
        <div class="col-sm-10">
            <select class="js-example-basic-multiple form-control" data-action="products" name="products_id[]" multiple="multiple">
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{__('Status')}}</label>
        <div class="col-sm-10">
            <select name="status" id="status" class="form-control ">
                <option value="1">{{__('Active')}}</option>
                <option value="2">{{__('Reject')}}</option>
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{__('Type')}}</label>
        <div class="col-sm-10">
            <select required name="type_id" id="type_id" class="form-control ">
                <option value="">{{__("Choose Type...")}}</option>
                
                @foreach ($types as $type)
                <option value="{{ $type->id }}" selected>{{ $type->name }}</option>
            @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    
  
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Value')}}</label>
        <div class="col-sm-10">
            
            <input  type="text" class="form-control " name="value" >
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Amount (Condition)')}}</label>
        <div class="col-sm-10">
            
            <input  type="text" class="form-control " name="amount" >
             <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">{{__('Starting Date')}}</label>
        <div class="col-sm-10">
            <input required class="form-control" name="starting_data" id="starting_data"/>
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">{{__('Ended Date')}}</label>
        <div class="col-sm-10">
            <input  class="form-control" name="ended_data" id="ended_data" required />
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
<script>$lang = "{{app()->getlocale()}}"</script>
<script>$id = ''</script>
<script>
    myDropzone('offers')
</script>
<script>
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
    });    

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
            url :$("meta[name='BASE_URL']").attr("content") + "/admin/offers",
            data : formdata,
            contentType : false,
            processData : false,
            cache : false,
            dataType : 'json',
            type : 'post'
        })
        .done(function(response) {
            if($myDropzone.files.length  != 0){
                $myDropzone.userId = response.data.offer_id
                // $myDropzone._token =  $("meta[name='csrf-token']").attr("content")
                $myDropzone.processQueue();
                $myDropzone.on("complete", function (file) {
                    if ($myDropzone.getUploadingFiles().length === 0 && $myDropzone.getQueuedFiles().length === 0) {
                        http.success({ 'message': response.message });
                        window.location.reload();
                    }
            });
            }else{
                http.success({ 'message': response.message });
                window.location.reload();
            }

        })
        .fail(function (response) {
            http.fail(response.responseJSON, true);
        });
});

setTimeout(() => {
    $("#starting_data").flatpickr();
    $("#ended_data").flatpickr();
}, 500);
</script>
<script>
    $('select[name="vendor_id"]').on('change', function (e) {  
        $('select[data-action="products"').html('');
        options_ar = '';
        options_en = '';
        $vendor_id =$(this).val();
        $.get($("meta[name='BASE_URL']").attr("content") + "/admin/vendors/products/" + $vendor_id ,
            function (data, textStatus, jqXHR) {
                data.forEach(element => {
                    options_ar += `<option value="${element.id}">${element.name['ar']}</option>`;
                    options_en += `<option value="${element.id}">${element.name['en']}</option>`;
                });
                if($lang == 'en'){
                    $('select[data-action="products"]').append(options_en)
                }else{
                    $('select[data-action="products"]').append(options_ar)

                }
            },
        );
    });
</script>
@endsection