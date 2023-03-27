@extends('layouts.app')
@section('content')
<form id="target" action="{{route('roles.store')}}" method="post" class="form-horizontal">
    @csrf
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name Arabic')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name_ar"  value="{{  $type->getTranslations('name')['ar'] }}" >
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name English')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name_en" value="{{ $type->getTranslations('name')['en']}}" >
            <p class="invalid-feedback"></p>
        </div>
        
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Belongs To Category')}}</label>
        <div class="col-sm-10">
            <select name="type_id" id="type_id" class="form-control js-example-basic-single" multiple>
                <option value="">{{__("Choose Type...")}}</option>
                @foreach ($types as $typ)
                <option @if (in_array($typ->id ,$ids ))
                    selected
                @endif value="{{ $typ->id  }}">{{ $typ->name }}</option>
                 @endforeach
            </select>
        </div>
        
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Add List')}}</label>
        <div class="col-sm-10">
            <a data-action="addInput">
                <span class="svg-icon svg-icon-primary svg-icon-2x"><!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo4/dist/../src/media/svg/icons/Navigation/Plus.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect fill="#000000" x="4" y="11" width="16" height="2" rx="1"/>
                        <rect fill="#000000" opacity="0.3" transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) " x="4" y="11" width="16" height="2" rx="1"/>
                    </g>
                </svg><!--end::Svg Icon--></span>
            </a>
        </i>
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
{{-- <script>list[] = {{$list}}</script> --}}
<script>$id = {{$type->id}}</script>

<script>
    $this = $('a[data-action="addInput"]');
    
    function list () {
    $.get($("meta[name='BASE_URL']").attr("content") + '/admin/category_attribute_types/' + $id, '',
    function (data, textStatus, jqXHR) {
        if(data){
            data.forEach(element => {
                $this.parent().append(`
                <div class="col-sm-10" style='margin-bottom: 10px;'>
                    <input type="text" class="form-control " name="list_name[]" value="${element}" >
                    </div>`);
                }); 
            } 
        },
        );
    }
        
    list();
    $('a[data-action="addInput"]').on('click',function (e) {  
        e.preventDefault();
        $(this).parent().append(`
        <div class="col-sm-10" style='margin-bottom: 10px;'>
            <input type="text" class="form-control " name="list_name[]" >
        </div>`);
    })
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
    var values = [];
    $("input[name='list_name[]']").each(function() {
        if($(this).val() != ''){
            values.push($(this).val());
        }
    });
    data = {
        _token: $("meta[name='csrf-token']").attr("content"),
        name_en: $.trim($this.find("input[name='name_en']").val()),
        name_ar: $this.find("input[name='name_ar']").val(),
        type_id: $this.find("select[name='type_id']").val(),
        list_name: values,
    }
    $this.find("button:submit").attr('disabled', true);
    $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');

    $.ajax({
        url: $("meta[name='BASE_URL']").attr("content") + '/admin/category_attribute_types/' + $id,
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