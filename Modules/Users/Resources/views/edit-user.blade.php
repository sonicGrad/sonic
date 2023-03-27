@extends('layouts.app')
@section('content')
<form id="target" action="{{route('users.store')}}" method="post" class="form-horizontal">
    @csrf
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
        <label   class="col-sm-2 control-label" for="" >{{ __('Type Of User') }}</label>
        <div class="col-sm-10">
            <select required name="role_id" id="role_id" class="form-control ">
                <option value="">{{__("Choose Type...")}}</option>
                @foreach ($roles as $role)
                <option value="{{ $role->name }}" @if( $user->hasRole($role->name)) selected  @endif>{{ $role->name }}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Sub Role') }}</label>
        <div class="col-sm-10">
            <select  name="sub_id" id="sub_id" class="js-example-basic-single form-control" multiple >
                <option value="">{{__("Choose Type...")}}</option>
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div  id="addition">
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
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Address')}}</label>
        <div class="col-sm-10">

            <input required type="text" class="form-control " name="address" placeholder="{{__('qaiat, dohad, ...')}}"   value="{{ $user->address}}">
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
    <div class="form-group location" style="display: none">
        <label class="col-sm-2 control-label">{{__('Location')}}</label>
        <div class="col-sm-offset-2 col-sm-10" id="map" style="width:100%;margin:20px auto;height:300px"> </div>
        <input name="location" value=""  />
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
<script>$type = "{{$user_role->name}}"</script>
<script> $sub_roles= {{$sub_roles}}</script>
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

<script>$lang = "{{app()->getLocale()}}"</script>
<script>$checkType = null</script>
<script>$vendor_id = null</script>
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
        address: $this.find("input[name='address']").val(),
        mobile_no: $.trim($this.find("input[name='mobile_no']").val()),
        email: $.trim($this.find("input[name='email']").val()),
        province_id: $this.find("select[name='province_id']").val(),
        type_id: $this.find("select[name='type_id']").val(),
        role_id: $this.find("select[name='role_id']").val(),
        company_name: $this.find("input[name='company_name']").val(),
        driving_license_no: $this.find("input[name='driving_license_no']").val(),
        driving_license_ended: $this.find("input[name='driving_license_ended']").val(),
        status_id: $this.find("select[name='status_id']").val(),
        sub_id: $this.find("select[name='sub_id']").val(),
        starting_time: $this.find("input[name='starting_time']").val(),
        closing_time: $this.find("input[name='closing_time']").val(),
        location: $this.find("input[name='location']").val(),
    }
    $this.find("button:submit").attr('disabled', true);
    $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');

    $.ajax({
        url: $("meta[name='BASE_URL']").attr("content") + '/admin/users/' + $id,
        type: 'PUT',
        data:data
    })
    .done(function(response) {
        if($('select[name="types_of_features"]').val()){
            data1 = {
                _token: $("meta[name='csrf-token']").attr("content"),
                typeable_id: $vendor_id,
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
    })
    .always(function () {
        $this.find("button:submit").attr('disabled', false);
        $this.find("button:submit").html(buttonText);
    });
});
</script>
<script>
    setTimeout(() => {
    vendor_type_id = '';
    vendor_data  = '';
    driver_data  = '';
    driver_type_id = '';
    status_options_en = '';
    status_options_ar = '';
    type_of_feature = '';
    feature_options = '';
    
    $.get($("meta[name='BASE_URL']").attr("content") + "/admin/vendors/user/" + $id , '',
    function (response, status) {
        vendor_data = response;
        $vendor_id = response.id;
        vendor_type_id = response.type_id ;
        if(response.type !=  null){
            $checkType = response.type.feature_type 
        }
        if(response.location !== undefined){
            $('div.location').css('display', 'block')
            response.location === null ?  initMap()  :initMap(Number(JSON.parse(response.location).lat), Number(JSON.parse(response.location).long))
        }
    });
    $.get($("meta[name='BASE_URL']").attr("content") + "/admin/drivers/driver-info/" + $id, '',
    function (response) { 
        driver_data = response;
        driver_type_id = response.type_id;
    });
    $.get($("meta[name='BASE_URL']").attr("content") + "/admin/features", '',
    function (response) { 
        response.forEach(element => {
            if(element.id == $checkType){
                feature_options += `<option value="${element.id}" selected>${element.name}</option>`;
            }else{
                feature_options += `<option value="${element.id}" >${element.name}</option>`;
            }
        });
    });
    $('#role_id').on('change', function () {
        $type = $(this).val();
        $('#addition').html('');
        sub($type);
        if($type == 'vendor'){
            $.get($("meta[name='BASE_URL']").attr("content") + "/admin/vendor_status", '',
            function (response, status) {
                response.forEach(element => {
                    if($lang == 'en'){
                       if(vendor_data.status_id == element.id){
                           status_options_en += `<option value="${element.id}" selected>${element.name.en}</option>`;
                       }else{
                           status_options_en += `<option value="${element.id}" >${element.name.en}</option>`;
                       }
                    }else{
                       if(vendor_data.status_id == element.id){
                           status_options_ar += `<option value="${element.id}" selected>${element.name.ar}</option>`;
                       }else{
                           status_options_ar += `<option value="${element.id}" >${element.name.ar}</option>`;
                       }
                   }
                });
            });
            
            $.get($("meta[name='BASE_URL']").attr("content") + "/admin/vendor_types", '',
            function (response, status) {
                option_en = '';
                option_ar = '';
                
                response.forEach(element => {
                   if($lang == 'en'){
                       if(vendor_type_id == element.id){
                           option_en += `<option value="${element.id}" selected>${element.name.en}</option>`;
                       }else{
                           option_en += `<option value="${element.id}" >${element.name.en}</option>`;
                       }
                    }else{
                       if(vendor_type_id == element.id){
                           option_ar += `<option value="${element.id}" selected>${element.name.ar}</option>`;
                       }else{
                           option_ar += `<option value="${element.id}"  >${element.name.ar}</option>`;
                       }
                   }
                //    option_en += `<option value="${element.id}">${element.name.en}</option>`;
                //    option_ar += `<option value="${element.id}">${element.name.ar}</option>`;
                });
                setTimeout(() => {
                    if($lang  == 'en'){

                    $('#addition').append(`
                    
                     <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" >Vendor Type</label>
                     <div class="col-sm-10">
                         <select name="type_id" id="type_id" class="form-control">
                             <option value="">Choose Type OF Vendor...</option>
                             ${option_en}
                        </select>
                         
                         <p class="invalid-feedback"></p>
                         
                     </div>
                     </div>
                     <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" >Campany Name</label>
                        <div class="col-sm-10">
                            <input required type="text" class="form-control" name="company_name" value=${vendor_data.company_name}>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-sm-2 control-label">Open At</label>
                        <div class="col-sm-10">
                            <input  class="form-control" name="starting_time" id="starting_time"  value=${vendor_data.starting_time}  />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Close At</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="closing_time" id="closing_time"  value=${vendor_data.closing_time} />
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" >Vendor Status</label>
                        <div class="col-sm-10">
                            <select name="status_id" id="status_id" class="form-control ">
                                <option value="">Choose Status OF Vendor...</option>
                                ${status_options_en}
                            </select>
                            
                            <p class="invalid-feedback"></p>
                            
                        </div>;
                     </div>
                    <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" >Vendor Features</label>
                        <div class="col-sm-10">
                            <select name="types_of_features" id="types_of_features" class="form-control  ">
                                <option value="">Choose Feature OF Vendor...</option>
                                ${feature_options}
                            </select>
                            
                            <p class="invalid-feedback"></p>
                            
                        </div>;
                     </div>
                    `);
                }else{
                    $('#addition').append(`
                    
                     <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" >نوع المورد</label>
                     <div class="col-sm-10">
                         <select name="type_id" id="type_id" class="form-control">
                             <option value="">أختر نوع المورد...</option>
                             ${option_ar}
    
                        </select>
                         
                         <p class="invalid-feedback"></p>
                         
                     </div>
                     </div>
                     <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" >اسم الشركة</label>
                        <div class="col-sm-10">
                            <input required type="text" class="form-control" name="company_name" value=${vendor_data.company_name}>
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-sm-2 control-label">تفتح في </label>
                        <div class="col-sm-10">
                            <input class="form-control" name="starting_time" id="starting_time"  value=${vendor_data.starting_time}  />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">تغلق في</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="closing_time" id="closing_time"  value=${vendor_data.closing_time} />
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" >حالة المورد</label>
                        <div class="col-sm-10">
                            <select name="status_id" id="status_id" class="form-control ">
                                <option value="">أختر حالة المورد...</option>
                                ${status_options_ar}

                            </select>
                            
                            <p class="invalid-feedback"></p>
                            
                        </div>
                     </div>
                     <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" >ميزات المورد</label>
                        <div class="col-sm-10">
                            <select name="types_of_features" id="types_of_features" class="form-control  ">
                                <option value="">Choose أختر ميزات المورد...</option>
                                ${feature_options}
                            </select>
                            
                            <p class="invalid-feedback"></p>
                            
                        </div>;
                     </div>
                    `); 
                }
                }, 500);
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
                $('select[name="types_of_features"]').on('change', function (e) {
                $value = $(this).val();
                data = {
                    _token: $("meta[name='csrf-token']").attr("content"),
                    typeable_type : 'Modules\\Vendors\\Entities\\Vendors',
                    feature_type : $value
                };
                $.get($("meta[name='BASE_URL']").attr("content") + "/admin/features/" + $vendor_id, data,
                    function (response, status) {
                    $('.feature').css('display', 'block');
                    $('input[name="stating_date"]').val(response.stating_date);
                    $('input[name="ended_date"]').val(response.ended_date);
                });
            });
            if($checkType != null){
                $('select[name="types_of_features"]').trigger('change')
            }     
            }, 500);
            })
        }else if($type == 'driver'){
            setTimeout(() => {
               $('div.location').css('display', 'block')
               driver_data.location === null ?  initMap() : initMap(Number(JSON.parse(driver_data.location).lat), Number(JSON.parse(driver_data.location).long))
               $('input[name="location"]').val(driver_data.location)
               $.get($("meta[name='BASE_URL']").attr("content") + "/admin/driver_status", '',
            function (response, status) {
                response.forEach(element => {
                    if($lang == 'en'){
                       if(driver_data.status_id == element.id){
                           status_options_en += `<option value="${element.id}" selected>${element.name.en}</option>`;
                       }else{
                           status_options_en += `<option value="${element.id}" >${element.name.en}</option>`;
                       }
                    }else{
                       if(driver_data.status_id == element.id){
                           status_options_ar += `<option value="${element.id}" selected>${element.name.ar}</option>`;
                       }else{
                           status_options_ar += `<option value="${element.id}" >${element.name.ar}</option>`;
                       }
                   }
                });
            });
            option_en = '';
            option_ar = '';
            driver_info = '';
            
            $.get($("meta[name='BASE_URL']").attr("content") + "/admin/driver_types", '',
            
            function (response, status) {
            response.forEach(element => {
                    
                    if($lang == 'en'){
                    if(driver_type_id == element.id){
                        option_en += `<option value="${element.id}" selected>${element.name.en}</option>`;
                       }
                       option_ar += `<option value="${element.id}" selected>${element.name.ar}</option>`;
                    }else{
                        if(driver_type_id == element.id){
                            option_en += `<option value="${element.id}" selected>${element.name.en}</option>`;
                        }
                       option_ar += `<option value="${element.id}" selected>${element.name.ar}</option>`;
                    }
                   option_en += `<option value="${element.id}">${element.name.en}</option>`;
                   option_ar += `<option value="${element.id}">${element.name.ar}</option>`;
                });
                if($lang  == 'en'){
                    $('#addition').append(`
                    <div class="form-group">

                    <label  class="col-sm-2 control-label" for="" >Driver Type</label>
                        <div class="col-sm-10">
                            <select name="type_id" id="type_id" class="form-control">
                                <option value="">Choose Type OF Driver...</option>
                                ${option_en}
                            </select>
                            
                            <p class="invalid-feedback"></p>
                            
                        </div>
                     </div>
                    
                     <div class="form-group">
                         <label  class="col-sm-2 control-label" for="" >Driving License Number</label>
                         <div class="col-sm-10">
                             <input required type="text" class="form-control" name="driving_license_no" value=${driver_data.driving_license_no} />
                         </div>
                     </div>
                     <div class="form-group">
                         <label  class="col-sm-2 control-label" for="" >Driving License Ended Date</label>
                         <div class="col-sm-10">
                             <input required type="text" class="form-control" name="driving_license_ended" value=${driver_data.driving_license_ended} >
                         </div>
                     </div>
                     <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" >Driver Status</label>
                        <div class="col-sm-10">
                            <select name="status_id" id="status_id" class="form-control ">
                                <option value="">Choose Status OF Driver...</option>
                                ${status_options_en}
                            </select>
                            
                            <p class="invalid-feedback"></p>
                            
                        </div>;
                     </div>
                    `);
                }else{
                    $('#addition').append(`
                    <div class="form-group">

                    <label  class="col-sm-2 control-label" for="" >نوع السائق</label>
                        <div class="col-sm-10">
                            <select name="type_id" id="type_id" class="form-control">
                                <option value="">أختر نوع السائق...</option>
                                ${option_ar}
        
                            </select>
                            
                            <p class="invalid-feedback"></p>
                            
                        </div>
                     </div>
                     
                     <div class="form-group">
                         <label  class="col-sm-2 control-label" for="" >رقم رخصة السائق</label>
                         <div class="col-sm-10">
                             <input required type="text" class="form-control" name="driving_license_no" value=${driver_data.driving_license_no} >
                         </div>
                     </div>
                     <div class="form-group">
                         <label  class="col-sm-2 control-label" for="" >تاريخ إنتهاء رخصة السائق</label>
                         <div class="col-sm-10">
                             <input required type="text" class="form-control" name="driving_license_ended" value=${driver_data.driving_license_ended} >
                         </div>
                     </div>
                     
                    `); 
                    

                    setTimeout(() => {
                        $('#addition').append(`
                        <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" >حالة السائق</label>
                        <div class="col-sm-10">
                            <select name="status_id" id="status_id" class="form-control ">
                                <option value="">أختر حالة السائق...</option>
                                ${status_options_ar}

                            </select>
                            
                            <p class="invalid-feedback"></p>
                            
                        </div>
                     </div>
                        `);
                    }, 1000);
                }
                setTimeout(() => {
                    $('input[name="driving_license_ended"]').flatpickr({
                        enableTime: false,
                        dateFormat: "Y-m-d H:i",
                    });
                }, 500);
            })
            .fail(function (response) {
                http.fail(response.responseJSON, true);
            });
           }, 1000);
        }else{
        $('#addition').html('');
        }
    });
    setTimeout(() => {
        $('#role_id').trigger('change');
    }, 100);

}, 500);


</script>

<script>
    function sub($id){
        roles_options_en = '<option value="">Choose Sub Role...</option>';
        roles_options_ar = '<option value="">Choose Sub Role...</option>';
        $('#sub_id').html('');
        $.get($("meta[name='BASE_URL']").attr("content") + "/admin/roles/sub-roles/" + $id, '',
        function (response, status) {
            response.forEach(element => { 
                if($sub_roles.includes(element.id)){
                    roles_options_en += `<option value="${element.name}" selected>${element.name}</option>`;
                    roles_options_ar += `<option value="${element.name}" selected>${element.label}</option>`;
                }else{
                    roles_options_en += `<option value="${element.name}">${element.name}</option>`;
                    roles_options_ar += `<option value="${element.name}">${element.label}</option>`;
                }
            });
            if($lang  == 'en'){
                $('#sub_id').append(roles_options_en)
            }else{
                $('#sub_id').appendroles_options_ar
            }
        });
    }
</script>
@endsection