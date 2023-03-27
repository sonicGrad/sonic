@extends('layouts.app')
@section('content')
<form id="target" action="{{route('categories.store')}}" method="post" class="form-horizontal">
    @csrf
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name Arabic')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name_ar" >
            
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name Endglish')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name_en" >
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Description Arabic')}}</label>
        <div class="col-sm-10">
            <Textarea required class="form-control " name="description_ar"></Textarea>
            <p class="invalid-feedback"></p>
           
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Description English')}}</label>
        <div class="col-sm-10">
            <Textarea required class="form-control " name="description_en"></Textarea>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Deafult Price')}}</label>
        <div class="col-sm-10">
            <input required class="form-control" name="price"/>
            <p class="invalid-feedback"></p>
           
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Product Code')}}</label>
        <div class="col-sm-10">
            <input required class="form-control " name="product_code"/>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Deafult Quantity')}}</label>
        <div class="col-sm-10">
            <input class="form-control  " name="quantity"/>
            
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Vendors Types') }}</label>
        <div class="col-sm-10">
            <select name="type_id" id="type_id" class="form-control ">
                <option value="">{{__("Choose Type...")}}</option>
                @foreach ($types as $types)
                <option value="{{ $types->id }}">{{ $types->name }}</option>
                 @endforeach
            </select>
            <p class="invalid-feedbac"></p>
            
        </div>
    </div>
    <div class="form-group vendor-name">
    </div>
    <div class="form-group categories">
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Feature Type') }}</label>
        <div class="col-sm-10">
            <select name="types_of_features" id="types_of_features" class="form-control  ">
                <option value="">{{__("Choose Status...")}}</option>
                @foreach ($types_of_features as $types_of_feature)
                <option value="{{ $types_of_feature->id }}" >{{ $types_of_feature->name }}</option>
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
    <hr>
    {{-- <div class="form-group">
        <label class="col-sm-2 control-label" style="font-size: 18px">{{__('Product Attribute')}}</label>
        <div class="col-md-10">
            <button class="btn btn-primary" data-action="addInput"><i class="fa-solid fa-plus"></i></button>
        </div>
    </div>
    <div class="form-group ">
        <div class="row">
            <div class="col-md-4">
                <label for="" class="col-md-4 control-label">{{__('Product Attribute 1')}}</label>
                <div class="col-sm-4">
                    <input class="form-control  " name="attribute_id[]" data-action="attr1"/>
                    
                    <p class="invalid-feedback"></p>
                </div>
                <div class="col-sm-4">
                    <input class="form-control  " name="value[]" data-action="value1"/>
                    
                    <p class="invalid-feedback"></p>
                </div>
            </div>
            <div class="col-md-4">
                <label for="" class="col-md-4 control-label">{{__('Product Attribute 2')}}</label>
                <div class="col-sm-4">
                    <input class="form-control  " name="attribute_id[]" data-action="attr2"/>
                    
                    <p class="invalid-feedback"></p>
                </div>
                <div class="col-sm-4">
                    <input class="form-control  " name="value[]" data-action="value2"/>
                    
                    <p class="invalid-feedback"></p>
                </div>
            </div>
            <div class="col-md-2">
                <label for="" class="col-md-4 control-label">{{__('Quantity')}}</label>
                <div class="col-sm-8">
                    <input class="form-control  " name="quantity[]"/>
                    
                    <p class="invalid-feedback"></p>
                </div>
            </div>
            <div class="col-md-2">
                <label for="" class="col-md-4 control-label">{{__('Addition Price')}}</label>
                <div class="col-sm-8">
                    <input class="form-control  " name="price[]"/>
                    
                    <p class="invalid-feedback"></p>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="form-group">
        <label class="col-sm-2 control-label" style="font-size: 18px">{{__('Product Attribute')}}</label>
        <div class="col-md-10">
            <button class="btn btn-primary" data-action="addInput"><i class="fa-solid fa-plus"></i></button>
        </div>
    </div>
    <div class="form-group " id="attribute">
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <input id="btn-submit" value="{{__('Add')}}" type="submit" class="btn btn-primary" >
        </div>
    </div>
</form>
@endsection

@section('js')
<script>$lang = "{{app()->getLocale()}}"</script>
{{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
<script>$id = ''</script>
{{-- <script>
    $('button[data-action="addInput"]').on('click',function (e) {  
        e.preventDefault();
        $(this).parent().append(`
        <div class="col-sm-10" style='margin-bottom: 10px;'>
            <input type="text" class="form-control " name="list_name[]" >
        </div>`);
    })
</script> --}}
<script>
  
</script>
<script>
   myDropzone('products')
 </script>
<script>
    setTimeout(() => {
        $('select[name="type_id"]').on('change', function (e) {  
        $('.vendor-name').html('');
        $('.categories').html('');
        $id = $(this).val();
        options = '';
        $.get($("meta[name='BASE_URL']").attr("content") + "/admin/vendor_types/vendors/" + $id, '',
            function (response, status) {
                response.forEach(element => {
                   options += `<option value="${element.id}">${element.company_name}</option>`;
                });
                if($lang  == 'en'){
                    $('.vendor-name').append(`
                    <label  class="col-sm-2 control-label" for="" >Vendor Name</label>
                        <div class="col-sm-10">
                            <select name="vendor_id" id="vendor_id" class="form-control  js-example-basic-single  ">
                                <option value="">Choose  Vendor...</option>
                                ${options}
                        </select>
                            
                            <p class="invalid-feedback"></p>
                            
                        </div>
                    `);
                }else{
                    $('.vendor-name').append(`
                    <label  class="col-sm-2 control-label" for="" >نوع المورد</label>
                        <div class="col-sm-10">
                            <select name="vendor_id" id="vendor_id" class="form-control js-example-basic-single  ">
                                <option value="">أختر نوع المورد...</option>
                                ${options}
    
                        </select>
                            
                            <p class="invalid-feedback"></p>
                            
                        </div>
                    `);
                }; 
                $(document).ready(function() {
                    $('.js-example-basic-single').select2();
                });
        });
        $.get($("meta[name='BASE_URL']").attr("content") + "/admin/categories/vendor-categories/" + $id, '',
            function (response, status) {
                options_ar_2 = '';
                options_en_2 = '';
                response.forEach(element => {
                    options_ar_2 += `<option value="${element.id}">${element.name.ar}</option>`;
                    options_en_2 += `<option value="${element.id}">${element.name.en}</option>`;
                });
                if($lang  == 'en'){
                    $('.categories').append(`
                    <label  class="col-sm-2 control-label" for="" >Category Type</label>
                        <div class="col-sm-10">
                            <select name="category_id" id="category_id" class="form-control  js-example-basic-single  ">
                                <option value="">Choose Type OF Category...</option>
                                ${options_en_2}
                        </select>
                            
                            <p class="invalid-feedback"></p>
                            
                        </div>
                    `);
                }else{
                    $('.categories').append(`
                    <label  class="col-sm-2 control-label" for="" >نوع التصنيف</label>
                        <div class="col-sm-10">
                            <select name="category_id" id="category_id" class="form-control js-example-basic-single  ">
                                <option value="">أختر نوع التصنيف...</option>
                                ${options_ar_2}
    
                        </select>
                            
                            <p class="invalid-feedback"></p>
                           
                        </div>
                    `);
                }; 
                $(document).ready(function() {
                    $('.js-example-basic-single').select2();
                });
        });
    });
    }, 1000);
</script>
<script>
    function appends(attrb){
        $('#attribute').append(`
                    <div class="row parent-div">
                        <div class="col-md-4 parent main-div">
                            <label for="" class="col-md-4 control-label">Product Attribute 1</label>
                            <div class="col-sm-4">
                                <select name="attribute_id[]" id="attribute_id[]" class="form-control js-example-basic-single">
                                    <option value="">choise..</option>
                                    ${attrb}
                                </select>
                            </div>
                            <div class="child"></div>
                            
                        </div>
                        <div class="col-md-4 parent main-div" >
                            <label for="" class="col-md-4 control-label">Product Attribute 2</label>
                            <div class="col-sm-4">
                                <select name="attribute_id[]" id="attribute_id[]" class="form-control js-example-basic-single">
                                    <option value="">choise..</option>
                                    ${attrb}
                                </select>
                            </div>
                            <div class="child"></div>
                        </div>
                        <div class="col-md-2 main-div">
                            <label for="" class="col-md-4 control-label">Quantity</label>
                            <div class="col-sm-8">
                                <input class="form-control  " name='quantity'/>
                                
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>
                        <div class="col-md-2 main-div">
                            <label for="" class="col-md-4 control-label">Price</label>
                            <div class="col-sm-8">
                                <input class="form-control  " name='price'/>
                                
                                <p class="invalid-feedback"></p>
                            </div>
                        </div>
                    </div>
                `)

                $(document).ready(function() {
                    $('.js-example-basic-single').select2();
                });
    }
    $("select[name='type_id']").on('change', function(e){
        e.preventDefault();
        $('#attribute').html('');

    })
    $('button[data-action="addInput"]').on('click',function (e) {
        e.preventDefault();
        $value = $("select[name='type_id']['#type_id']").val();
        addChild($value)
    })
    function addChild($value){
        console.log($value);

        $.get($("meta[name='BASE_URL']").attr("content") + "/admin/category_attribute_types/category/" + $value, "",
            function (data, textStatus, jqXHR) {
                attributes_en = '';
                attributes_ar = '';
                data.forEach(element => {
                    attributes_en +=  `<option value="${element.attribute.id}">${element.attribute.name.en}</option>`;  
                    attributes_ar +=  `<option value="${element.attribute.id}">${element.attribute.name.ar}</option>`;  
                });
                if($lang == 'ar'){
                    appends(attributes_ar)
                }else{
                    appends(attributes_en)
                }
                valuesAttbute()
            },
        );
    }
   function valuesAttbute(){
    $("select[name='attribute_id[]']").on('change', function (e) {  
        $value = $(this).val();
        $this = $(this);
        $this.closest('div.parent').find('.child').text('');
        $.get($("meta[name='BASE_URL']").attr("content") + "/admin/category_attribute_types/" + $value, "",
        function (data, textStatus, jqXHR) {
            if(data == ''){
            $this.closest('div.parent').find('.child').append(`
            <div class="col-sm-4">
                <input class="form-control  " name="values[]" />
                
                <p class="invalid-feedback"></p>
            </div>`
            )
            }else{
                
                values = ''
                data.forEach(element => {
                    values +=  `<option value="${element}">${element}</option>`;  
                });
                $this.closest('div.parent').find('.child').append(`
                <div class="col-sm-4">
                    <select name="values[]" id="values[]" class="form-control js-example-basic-single">
                        <option value="">choise..</option>
                        ${values}
                    </select>
                </div>`
                )
                
            }
        });
    });
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
        attributes = [];
        $('.parent-div').each(function () {  
            $this1 = $(this).find(".main-div");
            data1 = {};
            $.each($this1, function (index, value) { 
               if(index == 0){
                $attibute1 = $(value).find("select[name='attribute_id[]']").val();
                $value1 = $(value).find("select[name='values[]']").val();
                if($value1 == undefined){
                    $value1 = $(value).find("input[name='values[]']").val();
                }
                if($value1 != '' && $attibute1 != ''){
                    data1.attibute1 =  $attibute1
                    data1.value1 =  $value1
                }else{
                    data1.attibute1 =  $attibute1
                    data1.value1 = null
                }
               }
               else if(index == 1){
                   $attibute2 = $(value).find("select[name='attribute_id[]']").val();
                   $value2 = $(value).find("select[name='values[]']").val();
                   if($value2 == undefined){
                       $value2 = $(value).find("input[name='values[]']").val();
                    }
                    if($value2 != '' && $attibute2 != ''){
                        data1.attibute2 =  $attibute2
                        data1.value2 =  $value2
                    }else{
                        data1.attibute2 = $attibute2
                        data1.value2 =  null

                    }
                }
                else if(index == 2){
                    $quantity = $(value).find("input[name='quantity']").val();
                    if($quantity){
                        data1.quantity = $quantity
                    }else{
                        data1.quantity = null
                    }
                }
                else if(index == 3){
                    $price = $(value).find("input[name='price']").val();
                    if($price){
                        data1.price = $price
                    }else{
                        data1.price = null
                    }
                }
            });
            if($value1 != null || $value2 != null  ){
                attributes.push(data1);
            }
        });
        buttonText = $this.text();

        data = {
            _token: $("meta[name='csrf-token']").attr("content"),
            name_ar: $.trim($this.find("input[name='name_ar']").val()),
            name_en: $.trim($this.find("input[name='name_en']").val()),
            price: $.trim($this.find("input[name='price']").val()),
            quantity: $.trim($this.find("input[name='quantity']").val()),
            product_code: $.trim($this.find("input[name='product_code']").val()),
            type_id: $.trim($this.find("select[name='type_id']").val()),
            vendor_id: $.trim($this.find("select[name='vendor_id']").val()),
            category_id: $.trim($this.find("select[name='category_id']").val()),
            description_ar: $.trim($this.find("textarea[name='description_ar']").val()),
            description_en: $.trim($this.find("textarea[name='description_en']").val()),
        }
        $this.find("button:submit").attr('disabled', true);
        $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');
    
        $.post($("meta[name='BASE_URL']").attr("content") + "/admin/products", data,
        function (response, status) {
            $id = response.data.id;
            $myDropzone.userId = $id
            $myDropzone.processQueue();
            dataAttrubute = {
                    _token: $("meta[name='csrf-token']").attr("content"),
                    product_id :  $id,
                    price: $.trim($this.find("input[name='price']").val()),
                    quantity: $.trim($this.find("input[name='quantity']").val()),
                    attributes : attributes,
                }
                $.post($("meta[name='BASE_URL']").attr("content") + "/admin/products/attributes" , dataAttrubute,
                function (response, status) {
                })
                .fail(function (response) {
                    http.fail(response.responseJSON, true);
                })
                .always(function () {
                    $this.find("button:submit").attr('disabled', false);
                    $this.find("button:submit").html(buttonText);
                });
            if($('select[name="types_of_features"]').val() != ''){
                data1 = {
                    _token: $("meta[name='csrf-token']").attr("content"),
                    typeable_id:response.data.id,
                    ended_date: $.trim($this.find("input[name='ended_date']").val()),
                    stating_date: $.trim($this.find("input[name='stating_date']").val()),
                    feature_type : $.trim($this.find("select[name='types_of_features']").val()),
                    typeable_type : 'Modules\\Products\\Entities\\Product',
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
    $('select[name="types_of_features"]').on('change', function (e) {
        console.log($(this).val());
        $('.feature').css('display', 'none');
        if($(this).val() != ''){
            $('.feature').css('display', 'block');
        }
    });
    setTimeout(() => {
        $("#stating_date").flatpickr();
        $("#ended_date").flatpickr();
    }, 500);
</script>
@endsection