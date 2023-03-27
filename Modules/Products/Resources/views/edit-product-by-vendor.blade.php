@extends('layouts.app')
@section('content')
<form id="target" action="" method="post" class="form-horizontal">
    @csrf
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name Arabic')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name_ar" value="{{$product->getTranslations('name')['ar']}}" >
            
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Name Endglish')}}</label>
        <div class="col-sm-10">
            
            <input required type="text" class="form-control " name="name_en" value="{{$product->getTranslations('name')['en']}}" >
            
                    <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Description Arabic')}}</label>
        <div class="col-sm-10">
            <Textarea required class="form-control " name="description_ar">{{$product->getTranslations('description')['ar']}}</Textarea>
            <p class="invalid-feedback"></p>
           
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Description English')}}</label>
        <div class="col-sm-10">
            <Textarea required class="form-control " name="description_en">{{$product->getTranslations('description')['en']}}</Textarea>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label  class="col-sm-2 control-label" for="" >{{ __('Category') }}</label>
        <div class="col-sm-10">
            <select name="category_id" id="category_id" class="form-control  ">
                <option value="">{{__("Choose Category...")}}</option>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}" value="{{ $category->id }}" @if($category->id   == $product->category_id ) selected  @endif >{{ $category->name }}</option>
                @endforeach
            </select>
            
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
        <label for="" class="col-sm-2 control-label">{{__('Default Price')}}</label>
        <div class="col-sm-10">
            <input required class="form-control" name="price"  value="{{$DefaultAttribute->variation->price ?? ''}}"/>
            <p class="invalid-feedback"></p>
           
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Product Code')}}</label>
        <div class="col-sm-10">
            <input required class="form-control " name="product_code" value="{{$product->product_code}}"/>
            <p class="invalid-feedback"></p>
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Default Quantity')}}</label>
        <div class="col-sm-10">
            <input class="form-control  " name="quantity" value="{{$DefaultAttribute->variation->quantity ?? ''}}"/>
            
            <p class="invalid-feedback"></p>
        </div>
    </div>
    @if (\Auth::user()->hasRole('vendor') && \Auth::user()->hasRole('main branch supplier'))
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Branches')}}</label>
        <div class="col-sm-10">
            <select name="branch_id" id="branch_id" class="form-control  ">
                <option value="">{{__("Choose Branch...")}}</option>
                @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @if($branch->id  == $product->vendor_id ) selected  @endif >
                    {{ $branch->company_name }}  [ {{$branch->user->province->name}} ]
                </option>
                @endforeach
            </select>
            
            <p class="invalid-feedback"></p>
        </div>
    </div>
    @endif
    <div class="form-group">
        <label class="col-sm-2 control-label" style="font-size: 18px">Product Attribute</label>
        <div class="col-md-10">
            <button class="btn btn-primary" data-action="addInput"><i class="fa-solid fa-plus"></i></button>
        </div>
    </div>
    
    <div class="form-group " id="attribute">
        @foreach ($attributes as $attribute)
        <div class="row parent-div">
            <div class="col-md-4 parent main-div">
                <label for="" class="col-md-4 control-label">Product Attribute 1</label>
                <div class="col-sm-4">
                    <select name="attribute_id[]" id="attribute_id[]" class="form-control disabled-select js-example-basic-single" @if (isset($attribute[0]['variation_id']))
                    data-varition={{$attribute[0]['variation_id']}}
                    @endif 
                    >
                        <option value="">choise..</option>
                        @foreach ($variation_attributes as $variation_attribute)
                        <option value="{{$variation_attribute->attribute->id}}"
                            @if (isset($attribute[0]['type_id']) )
                                @if ($attribute[0]['type_id'] == $variation_attribute->attribute->id ) selected @endif 
                            @endif
                            >{{$variation_attribute->attribute->name}}</option> 
                        @endforeach
                    </select>
                </div>
                <div class="child">
                    @if (isset($attribute[0]['list']))
                        @if (count($attribute[0]['list']) != 0)
                        <div class="col-sm-4">
                            <select name="values[]" id="values[]" class="form-control disabled-select js-example-basic-single">
                                <option value="">choise..</option>
                                @foreach ($attribute[0]['list'] as $item)
                                    <option value="{{$item->name}}" @if ($attribute[0] != "" && $item->name == $attribute[0]['value'])
                                        selected
                                    @endif>{{$item->name}}</option>  
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div class="col-sm-4">
                            <input class="form-control  disabled-select " name="values[]" @if ($attribute[0]['value'] )
                            value="{{ $attribute[0]['value'] }}"
                            @endif  />

                            <p class="invalid-feedback"></p>
                        </div>
                        @endif
                        
                    @endif
                </div>
                
            </div>
            <div class="col-md-4 parent main-div" >
                <label for="" class="col-md-4 control-label">Product Attribute 2</label>
                <div class="col-sm-4">
                    <select name="attribute_id[]" id="attribute_id[]" class="form-control disabled-select js-example-basic-single">
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
                        <div class="col-sm-4">
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
                        <div class="col-sm-4">
                            <input class="form-control disabled-select  " name="values[]" @if ($attribute[1]['value'] )
                            value="{{ $attribute[1]['value'] }}"
                            @endif  />

                            <p class="invalid-feedback"></p>
                        </div>
                        @endif
                        
                    @endif
                </div>
            </div>
            <div class="col-md-2 main-div">
                <label for="" class="col-md-4 control-label">Quantity</label>
                <div class="col-sm-8">
                    <input class="form-control  " name='quantity'
                     @if ($attribute[0]['quantity'] || $attribute[0]['quantity'] === 0)
                    value="{{ $attribute[0]['quantity'] }}"
                    @endif  />
                    
                    <p class="invalid-feedback"></p>
                </div>
            </div>
            <div class="col-md-2 main-div">
                <label for="" class="col-md-4 control-label">Price</label>
                <div class="col-sm-5">
                    <input class="form-control  " name='price'
                    @if ($attribute[0]['price'] )
                    value="{{ $attribute[0]['price'] }}"
                    @endif 
                    />
                    
                    <p class="invalid-feedback"></p>
                </div>
                @if (isset($attribute[0]['variation_id']))
                 <a data-action="destroy" data-id={{$attribute[0]['variation_id']}} style="margin :10px" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> </a>
                @endif 
               <div class="col-sm-2" style="color: red">
               </div>
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
@endsection

@section('js')
<script>$id = {{$product->id}}</script>
<script>attributes = {!! $attributes->toJson() !!}</script>

<script>$lang = "{{app()->getLocale()}}"</script>

<script>
    function appends(attr1 ,attr2){
        $('#attribute').append(`
                <div class="row parent-div" >
                    <div class="col-md-4 parent main-div">
                        <label for="" class="col-md-4 control-label">Product Attribute 1</label>
                        <div class="col-sm-4">
                            <select name="attribute_id[]" id="attribute_id[]" class="form-control js-example-basic-single">
                                <option value="">choise..</option>
                                ${attr1}
                            </select>
                        </div>
                        <div class="child"></div>
                        
                    </div>
                    <div class="col-md-4 parent main-div" >
                        <label for="" class="col-md-4 control-label">Product Attribute 2</label>
                        <div class="col-sm-4">
                            <select name="attribute_id[]" id="attribute_id[]" class="form-control js-example-basic-single">
                                <option value="">choise..</option>
                                ${attr2}
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
    $("select[name='category_id']").on('change', function (e) {  
        $value = $(this).val();
        $('#attribute').text('');
        addChild($value)
    });
    $('button[data-action="addInput"]').on('click',function (e) {  
        e.preventDefault();
        $value = $("select[name='category_id']").val()
        addChild($value)
    })
    function addChild($value, $key1 = 0, $key2 = 0, value1 = 0 ,value2 = 0, element=''){
        setTimeout(() => {
                console.log('object');
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
   function addValues(element, $val){
        $value = $(element).val();
        $this = $(element)
        $this.closest('div.parent').find('.child').text('');
        $.get($("meta[name='BASE_URL']").attr("content") + "/admin/category_attribute_types/" + $value, "",
        function (data, textStatus, jqXHR) {
            if(data == ''){
            $this.closest('div.parent').find('.child').append(`
            <div class="col-sm-4">
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
                <div class="col-sm-4">
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
{{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
<script>
   myDropzone('products')
  imageRemoveAndAppeared('products', $id)

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
        buttonText = $this.text();
        attributes = [];
        $('.parent-div').each(function () {  
            $this1 = $(this).find(".main-div");
            data = {};
            $.each($this1, function (index, value) { 
               if(index == 0){
                $attibute1 = $(value).find("select[name='attribute_id[]']").val();
                $select1 = $(value).find("select[name='attribute_id[]']");
                $variation_id = $select1.attr('data-varition');
                if($variation_id != undefined){
                    data.variation_id = $variation_id
                }
                $value1 = $(value).find("select[name='values[]']").val();
                if($value1 == undefined){
                    $value1 = $(value).find("input[name='values[]']").val();
                }
                if($value1 != '' && $attibute1 != ''){
                    data.attibute1 =  $attibute1
                    data.value1 =  $value1
                }else{
                    data.attibute1 =  $attibute1
                    data.value1 = null
                }
               }
               else if(index == 1){
                   $attibute2 = $(value).find("select[name='attribute_id[]']").val();
                   $value2 = $(value).find("select[name='values[]']").val();
                   if($value2 == undefined){
                       $value2 = $(value).find("input[name='values[]']").val();
                    }
                    if($value2 != '' && $attibute2 != ''){
                        data.attibute2 =  $attibute2
                        data.value2 =  $value2
                    }else{
                        data.attibute2 = $attibute2
                        data.value2 =  null

                    }
                }
                else if(index == 2){
                    $quantity = $(value).find("input[name='quantity']").val();
                    if($quantity){
                        data.quantity = $quantity
                    }else{
                        data.quantity = null
                    }
                }
                else if(index == 3){
                    $price = $(value).find("input[name='price']").val();
                    if($price){
                        data.price = $price
                    }else{
                        data.price = null
                    }
                }
                });
                if($value1 != null || $value2 != null  ){
                    // console.log('value 1: ' + $value1);
                    // console.log('value 2: ' + $value2);
                    attributes.push(data);
                }
        });
        console.log(attributes);
        data1 = {
            _token: $("meta[name='csrf-token']").attr("content"),
            name_ar: $.trim($this.find("input[name='name_ar']").val()),
            name_en: $.trim($this.find("input[name='name_en']").val()),
            price: $.trim($this.find("input[name='price']").val()),
            quantity: $.trim($this.find("input[name='quantity']").val()),
            product_code: $.trim($this.find("input[name='product_code']").val()),
            category_id: $.trim($this.find("select[name='category_id']").val()),
            status_id: $.trim($this.find("select[name='status_id']").val()),
            description_ar: $.trim($this.find("textarea[name='description_ar']").val()),
            description_en: $.trim($this.find("textarea[name='description_en']").val()),
            branch_id: $.trim($this.find("select[name='branch_id']").val()),
        }
        $this.find("button:submit").attr('disabled', true);
        $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');
    
        $.post($("meta[name='BASE_URL']").attr("content") + "/admin/products/update-for-vendor/" + $id, data1,
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
                    http.success({ 'message': response.message });
                    // window.location.reload();
                })
                .fail(function (response) {
                    http.fail(response.responseJSON, true);
                })
                .always(function () {
                    $this.find("button:submit").attr('disabled', false);
                    $this.find("button:submit").html(buttonText);
                });
            http.success({ 'message': response.message });
            // window.location.reload();
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
                    // window.location.reload();
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