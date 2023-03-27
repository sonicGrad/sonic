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
<form id="target" action="{{route('products.store')}}" method="post" class="form-horizontal">
    @csrf
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name Arabic')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name_ar"  value="{{$product->getTranslations('name')['ar']}}"/>
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name English')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name_en" value="{{ $product->getTranslations('name')['en']}}"/>
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Description Arabic')}}</label>
        <div class="col-sm-10">
            <Textarea required class="form-control " name="description_ar">{{ $product->getTranslations('description')['ar']}}</Textarea>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Description English')}}</label>
        <div class="col-sm-10">
            <Textarea  required class="form-control " name="description_en">{{ $product->getTranslations('description')['en'] }}</Textarea>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Price')}}</label>
        <div class="col-sm-10">
            <input required class="form-control " name="price" value="{{$DefaultAttribute->variation->price ?? ''}}"/>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Product Code')}}</label>
        <div class="col-sm-10">
            <input required class="form-control " name="product_code"  value="{{$product->product_code}}"/>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Quantity')}}</label>
        <div class="col-sm-10">
            <input class="form-control " name="quantity" value="{{$DefaultAttribute->variation->quantity ?? ''}}"/>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Status') }}</label>
        <div class="col-sm-10">
            <select name="status_id" id="status_id" class="form-control ">
                <option value="">{{__("Choose Status...")}}</option>
                @foreach ($statuses as $status)
                 <option value="{{ $status->id }}" @if($status->id   == $product->status_id ) selected  @endif>{{ $status->name }}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Admin Status') }}</label>
        <div class="col-sm-10">
            <select name="admin_status" id="admin_status" class="form-control ">
                <option value="">{{__("Choose Status...")}}</option>
                @foreach ($admin_statuses as $admin_status)
                 <option value="{{ $admin_status->id }}" @if($admin_status->id   == $product->admin_status ) selected  @endif>{{ $admin_status->name }}</option>
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
                @foreach ($types as $type)
                 <option value="{{ $type->id }}" @if($type->id   == $product->vendor->type_id ) selected  @endif>{{ $type->name }}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group vendor-name">
        <label  class="col-sm-2 control-label" for="" >{{__('Vendor Name')}}</label>
        <div class="col-sm-10">
            <select name="vendor_id" id="vendor_id" class="form-control  js-example-basic-single ">
                <option value="">{{__('Vendor Name')}}</option>
                @foreach ($vendors as $vendor)
                    <option value="{{ $vendor->id }}" @if($vendor->id   ==  $product->vendor_id ) selected  @endif>{{ $vendor->company_name }}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group categories">
        <label  class="col-sm-2 control-label" for="" >{{__('Category')}}</label>
        <div class="col-sm-10">
            <select name="category_id" id="category_id" class="form-control  js-example-basic-single ">
                <option value="">{{__('Choose Category...')}}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @if($category->id   == $product->category_id ) selected  @endif>{{ $category->name }}</option>
                @endforeach
            </select>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Feature Type') }}</label>
        <div class="col-sm-10">
            <select name="types_of_features" id="types_of_features" class="form-control ">
                <option value="">{{__("Choose Status...")}}</option>
                @foreach ($types_of_features as $types_of_feature)
                @if ($product->type)
                <option value="{{ $types_of_feature->id }}" @if($types_of_feature->id  == $product->type->feature_type ) selected  @endif>{{ $types_of_feature->name }}</option>
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
                <input class="form-control" name="stating_date" id="stating_date" value=""  />
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-sm-2 control-label">{{__('Ended At')}}</label>
            <div class="col-sm-10">
                <input class="form-control" name="ended_date" id="ended_date" value="" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label" style="font-size: 18px">Product Attribute</label>
        <div class="col-md-10">
            <button class="btn btn-primary" data-action="addInput"><i class="fa fa-plus" aria-hidden="true"></i></button>
        </div>
    </div>
    
    <div class="form-group " id="attribute">
        @foreach ($attributes as $attribute)
        <div class="row parent-div">
            <div class="col-md parent main-div">
                <label for="" class="col-md control-label">Product Attribute 1</label>
                <div class="col-sm">
                    <select name="attribute_id[]" id="attribute_id[]" class="form-control disabled-select js-example-basic-single" @if (isset($attribute[0]['variation_id']))
                    data-varition={{$attribute[0]['variation_id']}}
                    @endif 
                    >
                    <option value="">choise..</option>
                    @foreach ($variation_attributes as $variation_attribute)
                    <option value="{{$variation_attribute->attribute->id}}"
                        @if (isset($attribute[0]['type_id']) )
                        {{-- {{dd($variation_attributes[1]->attribute->id )}} --}}
                            @if ($attribute[0]['type_id'] == $variation_attribute->attribute->id ) selected @endif 
                            @endif
                            >{{$variation_attribute->attribute->name}}</option> 
                        @endforeach
                    </select>
                </div>
                <div class="child">
                    @if (isset($attribute[0]['list']))
                        @if (count($attribute[0]['list']) != 0)
                        <div class="col-sm">
                            <select name="values[]" id="values[]" class="form-control disabled-select  js-example-basic-single">
                                <option value="">choise..</option>
                                @foreach ($attribute[0]['list'] as $item)
                                    <option value="{{$item->name}}" @if ($attribute[0] != "" && $item->name == $attribute[0]['value'])
                                        selected
                                    @endif>{{$item->name}}</option>  
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div class="col-sm">
                            <input class="form-control  " name="values[]" @if ($attribute[0]['value'] )
                            value="{{ $attribute[0]['value'] }}"
                            @endif  />

                            <p class="invalid-feedback"></p>
                        </div>
                        @endif
                        
                    @endif
                </div>
                
            </div>
            <div class="col-md parent main-div" >
                <label for="" class="col-md control-label">Product Attribute 2</label>
                <div class="col-sm">
                    <select name="attribute_id[]" id="attribute_id[]" class="form-control  disabled-select  js-example-basic-single">
                        <option value="">choise..</option>
                        @foreach ($variation_attributes as $variation_attribute)
                        <option value="{{$variation_attribute->attribute->id}}"
                            @if (isset($attribute[1]['type_id']) )
                                @if ($attribute[1]['type_id'] == $variation_attribute->attribute->id ) selected @endif 
                            @endif
                            >{{$variation_attribute->attribute->name}}</option> 
                        @endforeach
                    </select>
                </div>
                <div class="child">
                    @if (isset($attribute[1]['list']))
                        @if (count($attribute[1]['list']) != 0)
                        <div class="col-sm">
                            <select name="values[]" id="values[]" class="form-control disabled-select js-example-basic-single">
                                <option value="">choise..</option>
                                @foreach ($attribute[1]['list'] as $item)
                                    <option value="{{$item->name}}" @if ($attribute[1] != "" && $item->name == $attribute[1]['value'])
                                        selected
                                    @endif>{{$item->name}}</option>  
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div class="col-sm">
                            <input class="form-control  disabled-select" name="values[]" @if ($attribute[1]['value'] )
                            value="{{ $attribute[1]['value'] }}"
                            @endif  />

                            <p class="invalid-feedback"></p>
                        </div>
                        @endif
                        
                    @endif
                </div>
            </div>
            <div class="col-md main-div">
                <label for="" class="col-md control-label">Quantity</label>
                <div class="col-sm-8">
                    <input class="form-control  " name='quantity'
                     @if ($attribute[0]['quantity'] || $attribute[0]['quantity'] === 0 )
                    value="{{ $attribute[0]['quantity'] }}"
                    @endif  />
                    
                    <p class="invalid-feedback"></p>
                </div>
            </div>
            <div class="col-md main-div">
                <label for="" class="col-md control-label">Addition Price</label>
                <div class="col-md">
                <input class="form-control  " name='price'
                @if ($attribute[0]['price'] )
                value="{{ $attribute[0]['price'] }}"
                @endif 
                />
                
                <p class="invalid-feedback"></p>
                
                </div>
               <div class="col-sm-2" style="color: red">
               </div>
            </div>
            <div class="col">
                @if (isset($attribute[0]['variation_id']))
                <a data-action="destroy" data-id={{$attribute[0]['variation_id']}} class="btn btn-xs btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></a>    
               @endif 
            </div>
        </div>
        @endforeach 
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
<script>$lang = "{{app()->getLocale()}}"</script>

<script>$id = {{$product->id}}</script>
<script>$checkType = {{$vendor->type_id}}</script>
<script>
    function appends(attr1 ,attr2){
        $('#attribute').append(`
                <div class="row parent-div" >
                    <div class="col-md parent main-div">
                        <label for="" class="col-md control-label">Product Attribute 1</label>
                        <div class="col-sm">
                            <select name="attribute_id[]" id="attribute_id[]" class="form-control js-example-basic-single">
                                <option value="">choise..</option>
                                ${attr1}
                            </select>
                        </div>
                        <div class="child"></div>
                        
                    </div>
                    <div class="col-md parent main-div" >
                        <label for="" class="col-md control-label">Product Attribute 2</label>
                        <div class="col-sm">
                            <select name="attribute_id[]" id="attribute_id[]" class="form-control js-example-basic-single">
                                <option value="">choise..</option>
                                ${attr2}
                            </select>
                        </div>
                        <div class="child"></div>
                    </div>
                    <div class="col-md main-div">
                        <label for="" class="col-md control-label">Quantity</label>
                        <div class="col-sm-8">
                            <input class="form-control  " name='quantity'/>
                            
                            <p class="invalid-feedback"></p>
                        </div>
                    </div>
                    <div class="col-md main-div">
                        <label for="" class="col-md control-label">Addition Price</label>
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
    $("select[name='type_id']").on('change', function (e) {  
        $value = $(this).val();
        $('#attribute').text('');
        addChild($value)
    });
    $('button[data-action="addInput"]').on('click',function (e) {  
        e.preventDefault();
        $value = $("select[name='type_id']").val()
        addChild($value)
    })
    function addChild($value, $key1 = 0, $key2 = 0, value1 = 0 ,value2 = 0, element=''){
        setTimeout(() => {
                console.log($value);
            }, 1000);
        $.get($("meta[name='BASE_URL']").attr("content") + "/admin/category_attribute_types/category/" + $value, "",
            function (data, textStatus, jqXHR) {
                attributes_en1 = '';
                attributes_ar1 = '';
                attributes_en2 = '';
                attributes_ar2 = '';
                data.forEach(element => {
                    if($key1 == element.attribute_type_id){
                        attributes_en1 +=  `<option value="${element.attribute.id}" selected>${element.attribute.name.en}</option>`;  
                        attributes_ar1 +=  `<option value="${element.attribute.id}" selected>${element.attribute.name.ar}</option>`;  
                    }else{
                        attributes_en1 +=  `<option value="${element.attribute.id}" >${element.attribute.name.en}</option>`;  
                        attributes_ar1 +=  `<option value="${element.attribute.id}" >${element.attribute.name.ar}</option>`;  
                    }

                    if($key2 == element.attribute_type_id){
                        attributes_en2 +=  `<option value="${element.attribute.id}" selected>${element.attribute.name.en}</option>`;  
                        attributes_ar2 +=  `<option value="${element.attribute.id}" selected>${element.attribute.name.ar}</option>`;  
                    }else{
                        attributes_en2 +=  `<option value="${element.attribute.id}">${element.attribute.name.en}</option>`;  
                        attributes_ar2 +=  `<option value="${element.attribute.id}">${element.attribute.name.ar}</option>`;  
                    }
                    
                });
                if($lang == 'ar'){
                    appends(attributes_ar1, attributes_ar2)
                }else{
                    appends(attributes_en1, attributes_en2)
                }
                valuesAttbute()
               
            });
           
            
        }
        function valuesAttbute($value){
        $("select[name='attribute_id[]']").on('change', function (e) {  
            $value = $(this).val();
            $this = $(this);
            $this.closest('div.parent').find('.child').text('');
            child =  $this.closest('div.parent').find('.child');
            $.get($("meta[name='BASE_URL']").attr("content") + "/admin/category_attribute_types/" + $value, "",
            function (data, textStatus, jqXHR) {
                if(data == ''){
                $this.closest('div.parent').find('.child').append(`
                <div class="col-sm">
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
                    <div class="col-sm">
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
   function addValues(element, $val){
        $value = $(element).val();
        $this = $(element)
        $this.closest('div.parent').find('.child').text('');
        $.get($("meta[name='BASE_URL']").attr("content") + "/admin/category_attribute_types/" + $value, "",
        function (data, textStatus, jqXHR) {
            if(data == ''){
            $this.closest('div.parent').find('.child').append(`
            <div class="col-sm">
                <input class="form-control  " name="values[]" value="${$val}" />
                
                <p class="invalid-feedback"></p>
            </div>`
            )
            }else{
                
                values = ''
                data.forEach(element => {
                    if(element == $val){
                        values +=  `<option value="${element}" selected>${element}</option>`;  
                    }else{
                        values +=  `<option value="${element}" >${element}</option>`;  
                    }
                });
                $this.closest('div.parent').find('.child').append(`
                <div class="col-sm">
                    <select name="values[]" id="values[]" class="form-control js-example-basic-single">
                        <option value="">choise..</option>
                        ${values}
                    </select>
                </div>`
                )
                
            }
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
                $select1 = $(value).find("select[name='attribute_id[]']");
                $variation_id = $select1.attr('data-varition');
                if($variation_id != undefined){
                    data1.variation_id = $variation_id
                }
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
                // console.log('value 1: ' + $value1);
                // console.log('value 2: ' + $value2);
                attributes.push(data1);
            }
        });
    var buttonText = $this.find('button:submit').text();
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
        status_id: $.trim($this.find("select[name='status_id']").val()),
        admin_status: $.trim($this.find("select[name='admin_status']").val()),
        description_ar: $.trim($this.find("textarea[name='description_ar']").val()),
        description_en: $.trim($this.find("textarea[name='description_en']").val()),
    }
    $this.find("button:submit").attr('disabled', true);
    $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');

    $.ajax({
        url: $("meta[name='BASE_URL']").attr("content") + '/admin/products/' + $id,
        type: 'PUT',
        data:data
    })
    .done(function(response) {
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
            console.log(response);
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
        if($('select[name="types_of_features"]').val() != ''){
            data1 = {
                    _token: $("meta[name='csrf-token']").attr("content"),
                    typeable_id: $id,
                    ended_date: $.trim($this.find("input[name='ended_date']").val()),
                    stating_date: $.trim($this.find("input[name='stating_date']").val()),
                    feature_type : $.trim($this.find("select[name='types_of_features']").val()),
                    typeable_type : 'Modules\\Products\\Entities\\Product',
                }
                $.post($("meta[name='BASE_URL']").attr("content") + "/admin/features", data1,
                function (response1, status) {
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
  myDropzone('products')
  imageRemoveAndAppeared('products', $id)
</script>

<script>
    $('select[name="types_of_features"]').on('change', function (e) {
        $value = $(this).val();
        data = {
            _token: $("meta[name='csrf-token']").attr("content"),
            typeable_type : 'Modules\\Products\\Entities\\Product',
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

<script>setTimeout(() => {
    $('a[data-action="destroy"]').on('click', function (e) {
        e.preventDefault();
        $id =$(this).attr("data-id");
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this imaginary file!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
            })
            .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: $("meta[name='BASE_URL']").attr("content") + '/admin/products/product-variation/delete/' + $id,
                    type: 'POST',
                    data:{
                      _token: $("meta[name='csrf-token']").attr("content"),
                    }
                })
                .done(function(response) {
                    http.success({ 'message': response.message });
                    window.location.reload();
                })
                .fail(function(response){
                http.fail(response.responseJSON, true);
                })
            } else {
                swal("Your imaginary file is safe!");
            }
            });
        });

    }, 1000);
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