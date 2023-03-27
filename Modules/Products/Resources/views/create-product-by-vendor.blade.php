@extends('layouts.app')
@section('content')
<form id="target" action="" method="post" class="form-horizontal">
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
        <label  class="col-sm-2 control-label" for="" >{{ __('Category') }}</label>
        <div class="col-sm-10">
            <select name="category_id" id="category_id" class="form-control  ">
                <option value="">{{__("Choose Category...")}}</option>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}" >{{ $category->name }}</option>
                @endforeach
            </select>
            
            <p class="invalid-feedback"></p>
            
        </div>
    </div>
    <div class="form-group">
        <label for="" class="col-sm-2 control-label">{{__('Default Price')}}</label>
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
        <label for="" class="col-sm-2 control-label">{{__('Default Quantity')}}</label>
        <div class="col-sm-10">
            <input class="form-control  " name="quantity"/>
            
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
                <option value="{{ $branch->id }}" @if($branch->user_id   == \Auth::user()->id ) selected  @endif >{{ $branch->company_name }}  [ {{$branch->user->province->name}} ]</option>
                @endforeach
            </select>
            
            <p class="invalid-feedback"></p>
        </div>
    </div>
    @endif
    <div class="form-group">
        <label class="col-sm-2 control-label" style="font-size: 18px">{{__('Product Attribute')}}</label>
        <div class="col-md-10">
            <button class="btn btn-primary" data-action="addInput"><i class="fa-solid fa-plus"></i></button>
        </div>
    </div>
    <div class="form-group " id="attribute">
        {{-- <div class="row">
            <div class="col-md-4">
                <label for="" class="col-md-4 control-label">Product Attribute 1</label>
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
                <label for="" class="col-md-4 control-label">Product Attribute 2</label>
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
                <label for="" class="col-md-4 control-label">Quantity</label>
                <div class="col-sm-8">
                    <input class="form-control  " name="quantity[]"/>
                    
                    <p class="invalid-feedback"></p>
                </div>
            </div>
            <div class="col-md-2">
                <label for="" class="col-md-4 control-label">Addition Price</label>
                <div class="col-sm-8">
                    <input class="form-control  " name="price[]"/>
                    
                    <p class="invalid-feedback"></p>
                </div>
            </div>
        </div> --}}
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
<script>$id = ''</script>
<script>$vendor_type = {{$vendor_type}}</script>
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
  
    $('button[data-action="addInput"]').on('click',function (e) {  
        e.preventDefault();
        addChild($vendor_type)
    })
    function addChild($value){
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
   myDropzone('products')
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
            data = {};
            $.each($this1, function (index, value) { 
               if(index == 0){
                $attibute1 = $(value).find("select[name='attribute_id[]']").val();
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
                attributes.push(data);
            }
        });
        buttonText = $this.text();
        data1 = {
            _token: $("meta[name='csrf-token']").attr("content"),
            name_ar: $.trim($this.find("input[name='name_ar']").val()),
            name_en: $.trim($this.find("input[name='name_en']").val()),
            price: $.trim($this.find("input[name='price']").val()),
            quantity: $.trim($this.find("input[name='quantity']").val()),
            product_code: $.trim($this.find("input[name='product_code']").val()),
            category_id: $.trim($this.find("select[name='category_id']").val()),
            description_ar: $.trim($this.find("textarea[name='description_ar']").val()),
            description_en: $.trim($this.find("textarea[name='description_en']").val()),
            branch_id: $.trim($this.find("select[name='branch_id']").val()),
        };
        console.log(data1);
        
        // }
        $this.find("button:submit").attr('disabled', true);
        $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');
        
        // $.post($("meta[name='BASE_URL']").attr("content") + "/admin/products/attributes" , dataAttrubute,
        // function (response, status) {
        //     console.log(response);
        // });
        // console.log(attributes);
        $.post($("meta[name='BASE_URL']").attr("content") + "/admin/products/store-for-vendor", data1,
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
                    window.location.reload();
                })
                .fail(function (response) {
                    http.fail(response.responseJSON, true);
                })
                .always(function () {
                    $this.find("button:submit").attr('disabled', false);
                    $this.find("button:submit").html(buttonText);
                });
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

</script>

@endsection