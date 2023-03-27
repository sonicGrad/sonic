@extends('layouts.app')
@section('content')
<form id="target" action="{{route('roles.store')}}" method="post" class="form-horizontal">
    @csrf
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name Arabic')}}</label>
        <div class="col-sm-10">
            
            <input type="text" required class="form-control " name="type_ar"  value="{{ $type->getTranslations('type')['ar']}}">
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name English')}}</label>
        <div class="col-sm-10">
            
            <input type="text" required class="form-control" name="type_en"  value="{{ $type->getTranslations('type')['en']}}">
            <p class="invalid-feedback"></p>
        </div>
        
    </div>
    <div class="card card-custom">
        <div class="card-header">
            <h3 class="card-title">
                {{__('Content Arabic')}}
            </h3>
        </div>
        <div class="form-group row">
            <div class="col-lg-9 col-md-9 col-sm-12">
                <div class="summernote1" id="kt_summernote_1"></div>
            </div>
        </div>
    </div>
    <div class="card card-custom">
        <div class="card-header">
            <h3 class="card-title">
                {{__('Content English')}}
            </h3>
        </div>
        <div class="form-group row">
            <div class="col-lg-9 col-md-9 col-sm-12">
                <div class="summernote1" id="kt_summernote_2"></div>
            </div>
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
<script src={{asset('/public/assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js')}}></script>
<script src={{asset('/public/assets/js/pages/crud/forms/editors/summernote.js')}}></script>
<script>$id = {{$type->id}}</script>
<script>
    function htmlDecode(input){
        var e = document.createElement('div');
        e.innerHTML = input;
        return e;
    }
</script>
<script>
     $.ajax({
        url: $("meta[name='BASE_URL']").attr("content") + '/admin/terms/' + $id,
        type: 'get',
    })
    .done(function(response) {
        $("div[id='kt_summernote_2").summernote('code',htmlDecode(response.content.en));
        $("div[id='kt_summernote_1").summernote('code',htmlDecode(response.content.ar));
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
    data = {
        _token: $("meta[name='csrf-token']").attr("content"),
        type_en: $.trim($this.find("input[name='type_en']").val()),
        type_ar: $.trim($this.find("input[name='type_ar']").val()),
        content_ar: $this.find("div[id='kt_summernote_1']").parent('div').find('.card-block').html(),
        content_en: $this.find("div[id='kt_summernote_2']").parent('div').find('.card-block').html(),
        content_text_ar: $this.find("div[id='kt_summernote_1']").parent('div').find('.card-block').text(),
        content_text_en: $this.find("div[id='kt_summernote_2']").parent('div').find('.card-block').text(),
    }
    $this.find("button:submit").attr('disabled', true);
    $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');
    $.ajax({
        url: $("meta[name='BASE_URL']").attr("content") + '/admin/terms/' + $id,
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
<script>
    // Class definition

    var KTSummernoteDemo = function () {
    // Private functions
        var demos1 = function () {
        $('.summernote1').summernote({
        height: 150
        });
        }
        var demos2 = function () {
        $('.summernote2').summernote({
        height: 150
        });
        }

        return {
        // public functions
        init: function() {
        demos1();
        demos2();
        }
        };
        }();

</script>
@endsection