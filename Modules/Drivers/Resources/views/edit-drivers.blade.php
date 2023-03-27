@extends('layouts.app')
@section('content')
<form id="target" action="{{route('roles.store')}}" method="post" class="form-horizontal">
    @csrf
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Driver License Number')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="driving_license_no"  value="{{ $driver->driving_license_no}}">
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">{{__('Driving License Ended')}}</label>
        <div class="col-sm-10">
            <input required class="form-control" name="driving_license_ended" id="driving_license_ended" value="{{$driver->driving_license_ended}}" />
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Province') }}</label>
        <div class="col-sm-10">
            <select required name="province_id" id="province_id" class="form-control ">
                <option value="">{{__("Choose Province...")}}</option>
                @foreach ($provinces as $province)
                <option value="{{ $province->id }}" @if($province->id  == $driver->user->province_id ) selected  @endif>{{ $province->name }}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Driver Types') }}</label>
        <div class="col-sm-10">
            <select required name="type_id" id="type_id" class="form-control">
                <option value="">{{__("Choose Type...")}}</option>
                @foreach ($types as $type)

                <option value="{{ $type->id }}" @if($type->id  == $driver->type_of_driver->id) selected  @endif>{{ $type->name }}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Driver Status') }}</label>
        <div class="col-sm-10">
            <select name="status_id" id="status_id" class="form-control ">
                <option value="">{{__("Choose Status...")}}</option>
                @foreach ($statuses as $status)

                <option value="{{ $status->id }}" @if($status->id  == $driver->status_id ) selected  @endif>{{ $status->name }}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Address')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="address"  value="{{ $driver->user->address }}">
            <p class="invalid-feedback"></p>
        </div>
        
    </div>
    {{-- <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Deactivated')}}</label>
        <div class="col-sm-10">
            <input type="checkbox" id="checkbox" class="form-control  ace ace-switch ace-switch-5 " @if($driver->deactivated) checked="checked"  @endif  value="1">
            <span class="lbl middle"></span>
            <p class="invalid-feedback"></p>
        </div>
        
    </div> --}}
    <div class="container">
        <h5>{{__('License Image')}}</h5>
        <div class="row" style="clear: both;margin: 18px auto; width:70%">
            <div class="col-12">
            <div class="dropzone" id="file-dropzone"></div>
            </div>
        </div>
    </div>
    <div class="form-group location" style="display: none">
        <label class="col-sm-2 control-label">{{__('Location')}}</label>
        <div class="col-sm-offset-2 col-sm-10" id="map" style="width:100%;margin:20px auto;height:300px"> </div>
        <input name="location" value="{{$driver->location}}"  />
    </div>
    <div class="form-group ">
        <div class="col-sm-offset-2 col-sm-10">
            <input id="btn-submit" value="{{__('Add')}}" type="submit" class="btn btn-primary" >
        </div>
    </div>
</form>
<div class="grid-container">
</div>
@endsection
@section('js')
<script>$id = {{$driver->id}}</script>
<script>$location = @json($driver->location)</script>
<script>
    function initMap(lat =31.469868, lng =  34.388081) {
        console.log(document.getElementById("map"));
         const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: { lat:lat , lng: lng },
         });
        var marker = new google.maps.Marker({
          position:{ lat:lat , lng: lng },
          map: map,
        });
        map.addListener("click", (mapsMouseEvent) => {
        marker.setPosition(mapsMouseEvent.latLng);
        $('input[name="location"]').val(JSON.stringify(renameKey(mapsMouseEvent.latLng.toJSON(),'lng', 'long'), null, 2) );
        });
    }
    window.initMap = initMap;
</script>
<script>
    if($location !== undefined){
    $('div.location').css('display', 'block')
    $location === null ?  initMap()  :initMap(Number(JSON.parse($location).lat), Number(JSON.parse($location).long))
}
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
            driving_license_no: $.trim($this.find("input[name='driving_license_no']").val()),
            driving_license_ended: $.trim($this.find("input[name='driving_license_ended']").val()),
            // deactivated: $('#checkbox').is(':checked'),
            type_id : $.trim($this.find("select[name='type_id']").val()),
            province_id : $.trim($this.find("select[name='province_id']").val()),
            status_id : $.trim($this.find("select[name='status_id']").val()),
            address : $.trim($this.find("input[name='address']").val()),
            location: $this.find("input[name='location']").val(),

        }
        $this.find("button:submit").attr('disabled', true);
        $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');

        $.ajax({
            url: $("meta[name='BASE_URL']").attr("content") + '/admin/drivers/' + $id,
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

    setTimeout(() => {
       
        $("#driving_license_ended").flatpickr({
            enableTime: false,
            dateFormat: "Y-m-d H:i",
        });
    }, 500);
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.0/min/dropzone.min.js"></script>

<script>
  Dropzone.options.fileDropzone = {
    url: $("meta[name='BASE_URL']").attr("content") + '/admin/drivers/license-image-add/' + $id,
    acceptedFiles: ".jpeg,.jpg,.png,.gif",
    addRemoveLinks: true,
    maxFilesize: 8,
    headers: {
    'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    removedfile: function(file)
    {
      var name = file.upload.filename;
      $.ajax({
        type: 'POST',
        url: $("meta[name='BASE_URL']").attr("content") + '/admin/drivers/license-image-remove/' + $id,
        data: { "_token": "{{ csrf_token() }}", name: name},
        success: function (data){
        },
        error: function(e) {
        }});
        var fileRef;
        return (fileRef = file.previewElement) != null ?
        fileRef.parentNode.removeChild(file.previewElement) : void 0;
    },
    success: function (file, response) {
    },
  }
</script>
<script>
    $.get($("meta[name='BASE_URL']").attr("content") + '/admin/drivers/' +  $id, {}, function (response, status) {
       response.forEach(element => {
        $('.grid-container').append(`
       <div class="grid-item"><div class="dz-preview dz-processing dz-image-preview dz-complete">  
            <div class="dz-image">
                <img data-dz-thumbnail="" alt="er_model.png" src="${element.url}" style="width: 130px;">
            </div>  
                
            <a class="dz-remove" href="" data-action="remove_image" data-id=${element.name}>Remove file</a>
        </div>
       `);
       });

    });
</script>
<script>
    setTimeout(() => {
     $('a[data-action="remove_image"').on('click', function (e) {  
         e.preventDefault();
         $name = $(this).attr('data-id');
         $.post($("meta[name='BASE_URL']").attr("content") + "/admin/drivers/license-image-remove/" + $name , {
           _token: $("meta[name='csrf-token']").attr("content"),
         },
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
    }, 1000);
 </script>
@endsection