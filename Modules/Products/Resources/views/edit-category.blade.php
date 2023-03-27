@extends('layouts.app')
@section('css')
<style>
    .grid-container {
      display: grid;
      grid-template-columns: auto auto auto auto auto auto;
      padding: 10px
      /* background-color: #2196F3; */
    }
    .grid-itm {
      background-color: rgba(255, 255, 255, 0.8);
      border: 1px solid rgba(0, 0, 0, 0.8);
      padding: 20px;
      font-size: 30px;
      text-align: center;
    }
    </style>
@endsection
@section('content')
<form id="target" action="{{route('categories.store')}}" method="post" class="form-horizontal">
    @csrf
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name Arabic')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name_ar"  value="{{ $category->getTranslations('name')['ar'] }}"/>
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name Endglish')}}</label>
        <div class="col-sm-10">
            
            <input  required type="text" class="form-control " name="name_en" value="{{ $category->getTranslations('name')['en']}}"/>
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Description Arabic')}}</label>
        <div class="col-sm-10">
            <Textarea class="form-control " name="description_ar">{{ $category->getTranslations('description')['ar']}}</Textarea>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Description English')}}</label>
        <div class="col-sm-10">
            <Textarea class="form-control " name="description_en">{{ $category->getTranslations('description')['en'] }}</Textarea>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Status') }}</label>
        <div class="col-sm-10">
            <select name="status_id" id="status_id" class="form-control ">
                <option value="">{{__("Choose Status...")}}</option>
                @foreach ($statuses as $status)
                 <option value="{{ $status->id }}" @if($status->id   == $category->status_id ) selected  @endif>{{ $status->name }}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Vendors Types') }}</label>
        <div class="col-sm-10">
            <select name="type_id" id="type_id" class="form-control ">
                <option value="">{{__("Choose Type...")}}</option>
                @foreach ($types as $types)
                <option value="{{ $types->id }}" @if($types->id   == $category->vendor_type ) selected  @endif>{{ $types->name }}</option>
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

    
</div>
@endsection
@section('js')
<script>$id = {{$category->id}}</script>
<script>
    imageRemoveAndAppeared('categories', $id);
    myDropzone('categories')


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
        name_ar: $.trim($this.find("input[name='name_ar']").val()),
        name_en: $.trim($this.find("input[name='name_en']").val()),
        type_id: $.trim($this.find("select[name='type_id']").val()),
        description_ar: $.trim($this.find("textarea[name='description_ar']").val()),
        description_en: $.trim($this.find("textarea[name='description_en']").val()),
    }
    $this.find("button:submit").attr('disabled', true);
    $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');

    $.ajax({
        url: $("meta[name='BASE_URL']").attr("content") + '/admin/categories/' + $id,
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

</script>

@endsection
{{-- <div class="grid-item"><div class="dz-preview dz-processing dz-image-preview dz-complete">  
    <div class="dz-image">
        <img data-dz-thumbnail="" alt="er_model.png" src="">
    </div>  
    <div class="dz-details">    
        <div class="dz-size">
            <span data-dz-size="">
                <strong>0.3</strong>
                 MB
            </span>
        </div>    
        <div class="dz-filename">
            <span data-dz-name="">er_model.png
                </span>
            </div>  
        </div>  
         
        <div class="dz-error-mark">    
            <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">      
                <title>Error</title>      
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">        
                    <g stroke="#747474" stroke-opacity="0.198794158" fill="#FFFFFF" fill-opacity="0.816519475">          
                        <path d="M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"></path>        
                    </g>      
                </g>    
            </svg>  
        </div>
        <a class="dz-remove" href="javascript:undefined;" data-dz-remove="">Remove file</a>
</div> --}}

{{-- images In DropZone --}}
{{-- <div class="dz-preview dz-processing dz-image-preview dz-complete">  
    <div class="dz-image">
        <img data-dz-thumbnail="" alt="er_model.png" src="">
    </div>  
    <div class="dz-details">    
        <div class="dz-size">
            <span data-dz-size="">
                <strong>0.3</strong>
                 MB
            </span>
        </div>    
        <div class="dz-filename">
            <span data-dz-name="">er_model.png
                </span>
            </div>  
        </div>  
        <div class="dz-progress">
            <span class="dz-upload" data-dz-uploadprogress="" style="width: 100%;">
            </span>
        </div>  
        <div class="dz-error-message">
            <span data-dz-errormessage=""></span>
        </div>  
        <div class="dz-success-mark">    
            <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">      <title>Check</title>      
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">        
                    <path d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475" fill="#FFFFFF">
                    </path>      
                </g>    
            </svg>  
        </div>  
        <div class="dz-error-mark">    
            <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">      
                <title>Error</title>      
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">        
                    <g stroke="#747474" stroke-opacity="0.198794158" fill="#FFFFFF" fill-opacity="0.816519475">          
                        <path d="M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"></path>        
                    </g>      
                </g>    
            </svg>  
        </div>
        <a class="dz-remove" href="javascript:undefined;" data-dz-remove="">Remove file</a>
</div> --}}