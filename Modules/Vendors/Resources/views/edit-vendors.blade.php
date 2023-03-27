@extends('layouts.app')
@section('content')
<form id="target" action="{{route('roles.store')}}" method="post" class="form-horizontal">
    @csrf
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
                <option value="{{ $province->id }}" @if($province->id  == $vendor->user->province_id ) selected  @endif>{{ $province->name }}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Vendors Types') }}</label>
        <div class="col-sm-10">
            <select required name="type_id" id="type_id" class="form-control ">
                <option value="">{{__("Choose Type...")}}</option>
                @foreach ($types as $type)
                <option value="{{ $type->id }}" @if($type->id  == $vendor->type_of_vendor->id) selected  @endif>{{ $type->name }}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Vendor Status') }}</label>
        <div class="col-sm-10">
            <select name="status_id" id="status_id" class="form-control ">
                <option value="">{{__("Choose Status...")}}</option>
                @foreach ($statuses as $status)

                <option value="{{ $status->id }}" @if($status->id  == $vendor->status_id) selected  @endif>{{ $status->name }}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Address')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control" name="address"  value="{{ $vendor->user->address }}">
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
    @if (\Auth::user()->hasRole('super_admin'))
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Feature Type') }}</label>
        <div class="col-sm-10">
            <select name="types_of_features" id="types_of_features" class="form-control ">
                <option value="">{{__("Choose Status...")}}</option>
                @foreach ($types_of_features as $types_of_feature)
                @if ($vendor->type)
                <option value="{{ $types_of_feature->id }}" @if($types_of_feature->id  == $vendor->type->feature_type ) selected  @endif>{{ $types_of_feature->name }}</option>
                @else
                <option value="{{ $types_of_feature->id }}" >{{ $types_of_feature->name }}</option>
                @endif
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="feature" style="display: none">
        <div class="form-group">
            <label class="col-sm-2 control-label">{{__('Stated At')}}</label>
            <div class="col-sm-10">
                <input  class="form-control" name="stating_date" id="stating_date" value=""  />
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-sm-2 control-label">{{__('Ended At')}}</label>
            <div class="col-sm-10">
                <input  class="form-control" name="ended_date" id="ended_date" value="" />
            </div>
        </div>
    </div>
    @endif
    <div class="form-group location" style="display: none">
        <label class="col-sm-2 control-label">{{__('Location')}}</label>
        <div class="col-sm-offset-2 col-sm-10" id="map" style="width:100%;margin:20px auto;height:300px"> </div>
        <input name="location" value="{{$vendor->location}}"  />
    </div>
    <div class="form-group ">
        <div class="col-sm-offset-2 col-sm-10">
            <input id="btn-submit" value="{{__('Add')}}" type="submit" class="btn btn-primary" >
        </div>
    </div>
</form>
@endsection
@section('js')
<script>$id = {{$vendor->id}}</script>
<script>$location = @json($vendor->location)</script>
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
    imageRemoveAndAppeared('vendors', $id)
    myDropzone('vendors')
    function imageRemoveAndAppeared(image_type, $id){
    $('form').after(`
    <div class="grid-container"></div>
    `)
    $.get($("meta[name='BASE_URL']").attr("content") + '/admin/' + image_type +'/images/' + $id, {}, function (response, status) {
        if(response){
            response.forEach(element => {
         $('.grid-container').append(`
        <div class="grid-item"><div class="dz-preview dz-processing dz-image-preview dz-complete image_div">  
             <div class="dz-image">
                 <img data-dz-thumbnail="" alt="er_model.png" src="${element.url}" style="width: 130px;">
             </div>  
             <a class="dz-remove" href="" data-action="remove_image" data-id=${element.name}>Remove file</a>
         </div>
        `);
        });
        }
 
     });
     console.log($("meta[name='csrf-token']").attr("content"));
    setTimeout(() => {
        $('a[data-action="remove_image"').on('click', function (e) {  
            e.preventDefault();
            $name = $(this).attr('data-id');
            $this = $(this);
            $.ajaxSetup({
                headers:{
                   'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                }
             })
             $.ajax({
                url: $("meta[name='BASE_URL']").attr("content") + "/admin/categories/image-remove/"+ $name ,
                type: 'DELETE',
                data:{
                  _token: $("meta[name='csrf-token']").attr("content"),
                }
            })
            .done(function(response) {
                http.success({ 'message': response.message });
                $this.parent().remove(); 
            })
            .fail(function(response){
            http.fail(response.responseJSON, true);
            })

        });
       }, 1000);
}
</script>
@if ($vendor->type)
<script>$checkType = {{$vendor->type->id}}</script>
@else
<script>$checkType = null</script>
    
@endif

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
            company_name: $.trim($this.find("input[name='company_name']").val()),
            starting_time: $.trim($this.find("input[name='starting_time']").val()),
            closing_time: $.trim($this.find("input[name='closing_time']").val()),
            // deactivated: $('#checkbox').is(':checked'),
            type_id : $.trim($this.find("select[name='type_id']").val()),
            province_id : $.trim($this.find("select[name='province_id']").val()),
            address : $.trim($this.find("input[name='address']").val()),
            status_id : $.trim($this.find("select[name='status_id']").val()),
            location: $this.find("input[name='location']").val(),

        }
        $this.find("button:submit").attr('disabled', true);
        $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');

        $.ajax({
            url: $("meta[name='BASE_URL']").attr("content") + '/admin/vendors/' + $id,
            type: 'PUT',
            data:data
        })
        .done(function(response) {
            $id = response.data.id;
            $myDropzone.userId = $id
            $myDropzone.processQueue();
            if($('select[name="types_of_features"]').val() != '' && $('select[name="types_of_features"]').val() != undefined){
                data1 = {
                    _token: $("meta[name='csrf-token']").attr("content"),
                    typeable_id: $id,
                    ended_date: $.trim($this.find("input[name='ended_date']").val()),
                    stating_date: $.trim($this.find("input[name='stating_date']").val()),
                    feature_type : $.trim($this.find("select[name='types_of_features']").val()),
                    typeable_type : 'Modules\\Vendors\\Entities\\Vendors',
                }
                $.post($("meta[name='BASE_URL']").attr("content") + "/admin/features", data1,
                function (response, status) {
                    http.success({ 'message': response.message });
                    window.location.reload();
                })
                .fail(function (response) {
                    http.fail(response.responseJSON, true);
                })
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
        $("#closing_time").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        });
        $("#starting_time").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
        });
    }, 500);
</script>
<script>
    $('select[name="types_of_features"]').on('change', function (e) {
        $value = $(this).val();
        data = {
            _token: $("meta[name='csrf-token']").attr("content"),
            typeable_type : 'Modules\\Vendors\\Entities\\Vendors',
            feature_type : $value
        };
        $.get($("meta[name='BASE_URL']").attr("content") + "/admin/features/" + $id, data,
        function (response, status) {
           $('.feature').css('display', 'block');
           $('input[name="stating_date"]').val(response.stating_date);
           $('input[name="ended_date"]').val(response.ended_date);
        });
            
    });
    setTimeout(() => {
        if($checkType != null){
            $('select[name="types_of_features"]').trigger('change')
        }
        $("#stating_date").flatpickr();
        $("#ended_date").flatpickr();
    }, 500);
</script>
@endsection