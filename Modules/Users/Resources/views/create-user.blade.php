@extends('layouts.app')
@section('content')
<form id="target" action="{{route('users.store')}}" method="post" class="form-horizontal" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('First Name')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control" name="first_name" >
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Last Name')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control" name="last_name" >
            <p class="invalid-feedback"></p>
        </div>
        
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('National Number | Passport Number')}}</label>
        <div class="col-sm-10">

            <input type="text" class="form-control " name="national_id" >
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Province') }}</label>
        <div class="col-sm-10">
            <select required name="province_id" id="province_id" class="form-control ">
                <option value="">{{__("Choose Province...")}}</option>
                @foreach ($provinces as $province)
                <option value="{{ $province->id }}">{{ $province->full_name }}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Type Of User') }}</label>
        <div class="col-sm-10">
            <select required name="role_id" id="role_id" class="form-control">
                <option value="">{{__("Choose Type...")}}</option>
                @foreach ($roles as $role)
                <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Sub Role') }}</label>
        <div class="col-sm-10">
            <select  name="sub_id" id="sub_id" class="js-example-basic-single form-control" multiple>
                <option value="">{{__("Choose Type...")}}</option>
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div  id="addition">
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Address')}}</label>
        <div class="col-sm-10">

            <input required type="text" class="form-control " name="address" placeholder="{{__('qaiat, dohad, ...')}}" >
            <p class="invalid-feedback"></p>
        </div>

    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Mobile Number')}}</label>
        <div class="col-sm-10">

            <input required type="text" class="form-control " name="mobile_no" >
            <p class="invalid-feedback"></p>
        </div>

    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Email')}}</label>
        <div class="col-sm-10">

            <input required type="text" class="form-control" name="email" >
            <p class="invalid-feedback"></p>
        </div>

    </div>
    <div class="feature" style="display: none">
        <div class="form-group">
            <label  class="col-sm-2 control-label" for="" >{{ __('Feature Type') }}</label>
            <div class="col-sm-10">
                <select name="types_of_features" id="types_of_features" class="form-control ">
                    <option value="">{{__("Choose Status...")}}</option>
                    @foreach ($types_of_features as $types_of_feature)
                    <option value="{{ $types_of_feature->id }}" >{{ $types_of_feature->name }}</option>
                    @endforeach
                </select>
                <p class="invalid-feedback"></p>
            </div>
        </div>
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
    
    <div class="form-group location" style="display: none">
        <label class="col-sm-2 control-label">{{__('Location')}}</label>
        <div class="col-sm-offset-2 col-sm-10" id="map" style="width:100%;margin:20px auto;height:300px"> </div>
        <input name="location" value="${data.location}"  />
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
        renameKey(mapsMouseEvent.latLng.toJSON(),'lng', 'long')
        // console.log(renameKey(mapsMouseEvent.latLng.toJSON(),'lng', 'long'));
        // console.log(mapsMouseEvent.latLng.toJSON().forEach(element => {
        //     renameKey(element,'lng', 'long')
        // }));
        $('input[name="location"]').val(JSON.stringify(renameKey(mapsMouseEvent.latLng.toJSON(),'lng', 'long'), null, 2) );
        // $location =  JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2)
        });
    }
    window.initMap = initMap;
</script>
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
    var formdata=new FormData($this[0]);
    $this.find("button:submit").attr('disabled', true);
    $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');
        $.ajax({
            enctype: 'multipart/form-data',
            url :$("meta[name='BASE_URL']").attr("content") + "/admin/users",
            data : formdata,
            contentType : false,
            processData : false,
            cache : false,
            dataType : 'json',
            type : 'post'
        })
        .done(function(response) {
            if($('select[name="types_of_features"]').val() != ''){
                data1 = {
                    _token: $("meta[name='csrf-token']").attr("content"),
                    typeable_id: response.id,
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
</script>

<script>
    setTimeout(() => {
        $('#role_id').on('change', function () { 
            $('.feature').css('display', 'none');
            status_options_en = '';
            status_options_ar = '';
            
            $id = $(this).val();
            $('#addition').html('');
             sub($id);
            
            if($id == 'driver'){
            $('div.location').css('display', 'block')
            initMap()
            $.get($("meta[name='BASE_URL']").attr("content") + "/admin/driver_status", '',
            function (response, status) {
                response.forEach(element => {
                    status_options_en += `<option value="${element.id}">${element.name.en}</option>`;
                    status_options_ar += `<option value="${element.id}">${element.name.ar}</option>`;
                });
            });
            
            $.get($("meta[name='BASE_URL']").attr("content") + "/admin/driver_types", '',
            function (response, status) {
                option_en = '';
                option_ar = '';
                response.forEach(element => {
                   option_en += `<option value="${element.id}">${element.name.en}</option>`;
                   option_ar += `<option value="${element.id}">${element.name.ar}</option>`;
                });
                setTimeout(() => {
                    if($lang  == 'en'){
                    
                    $('#addition').append(`
                    <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" >Driver Type</label>
                        <div class="col-sm-10">
                            <select name="type_id" id="type_id" class="form-control ">
                                <option value="">Choose Type OF Driver...</option>
                                ${option_en}
                            </select>
                            <p class="invalid-feedback"></p>
                        </div>;
                     </div>
                     <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" >Driving License Number</label>
                        <div class="col-sm-10">
                            <input  required type="text" class="form-control" name="driving_license_no" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" >Driving License Ended Date</label>
                        <div class="col-sm-10">
                            <input required type="text" class="form-control" name="driving_license_ended" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" > Driver License Image</label>
                        <div class="col-sm-10">
                            <input  type="file" class="form-control" name="driving_license_image" >
                        </div>
                    </div>
                    
                    `);
                    setTimeout(() => {
                        $('#addition').append(` 
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
                    }, 1000);
                    setTimeout(() => {
                        $('input[name="driving_license_ended"]').flatpickr({
                            enableTime: false,
                            dateFormat: "Y-m-d H:i",
                        });
                    }, 500);
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
                            <input required type="text" class="form-control" name="driving_license_no" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" >تاريخ إنتهاء رخصة السائق</label>
                        <div class="col-sm-10">
                            <input required type="text" class="form-control" name="driving_license_ended" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="col-sm-2 control-label" for="" > صورة رخصة السائق</label>
                        <div class="col-sm-10">
                            <input type="file" class="form-control" name="driving_license_image" >
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
                    }, 100);
                    setTimeout(() => {
                        $('input[name="driving_license_ended"]').flatpickr({
                            enableTime: false,
                            dateFormat: "Y-m-d H:i",
                        });
                    }, 500);
                }
                }, 500);
            })
            .fail(function (response) {
                http.fail(response.responseJSON, true);
            });
            
        }else if($id == 'vendor'){
            $('div.location').css('display', 'block')
            initMap()
           $('.feature').css('display', 'block');
            $.get($("meta[name='BASE_URL']").attr("content") + "/admin/vendor_status", '',
            function (response, status) {
                response.forEach(element => {
                    status_options_en += `<option value="${element.id}">${element.name.en}</option>`;
                    status_options_ar += `<option value="${element.id}">${element.name.ar}</option>`;
                });
            });
          
            $.get($("meta[name='BASE_URL']").attr("content") + "/admin/vendor_types", '',
            function (response, status) {
                option_en = '';
                option_ar = '';
                response.forEach(element => {
                   option_en += `<option value="${element.id}">${element.name.en}</option>`;
                   option_ar += `<option value="${element.id}">${element.name.ar}</option>`;
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
                            <input required type="text" class="form-control" name="company_name" >
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-sm-2 control-label">Open At</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="starting_time" id="starting_time"    />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Close At</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="closing_time" id="closing_time"  />
                        </div>
                    </div>
                  
                            `);
                        setTimeout(() => {
                            $('#addition').append(` 
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
                    `);
                    }, 1000);
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
                            <input required type="text" class="form-control" name="company_name" >
                        </div>
                     </div>
                     <div class="form-group">
                        <label class="col-sm-2 control-label">تفتح في </label>
                        <div class="col-sm-10">
                            <input class="form-control" name="starting_time" id="starting_time"  />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">تغلق في</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="closing_time" id="closing_time"   />
                        </div>
                    </div>
                    `);
                    setTimeout(() => {
                        $('#addition').append(` 
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
                    `);
                    }, 1000); 
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
            }, 500);
            })
            .fail(function (response) {
                http.fail(response.responseJSON, true);
            });
        }else{
            $('div.location').css('display', 'none')
            $('#addition').html('');

        }
    });
   }, 1000);
   setTimeout(() => {
        $("#stating_date").flatpickr();
        $("#ended_date").flatpickr();
    }, 500);
</script>
<script>
    function sub($id){
        roles_options_en = '';
        roles_options_ar = '';
        $('#sub_id').html('');
        $.get($("meta[name='BASE_URL']").attr("content") + "/admin/roles/sub-roles/" + $id, '',
        function (response, status) {
            response.forEach(element => {
                roles_options_en += `<option value="${element.name}">${element.name}</option>`;
                roles_options_ar += `<option value="${element.name}">${element.label}</option>`;
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