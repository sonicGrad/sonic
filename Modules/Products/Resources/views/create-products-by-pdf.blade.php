@extends('layouts.app')
@section('content')
    <form id="target" action="{{route('roles.store')}}" method="post"  enctype="multipart/form-data" class="form-horizontal">
        @csrf
        <div class="form-group">
            <div class="col-sm-10" style="margin-left: 50px">
                <h1>{{__('Imprting Excel')}}</h1>
            </div>
        </div>
        <div class="form-group">
            <label  class="col-sm-2 control-label" for="" >{{ __('Vendors Types') }}</label>
            <div class="col-sm-10">
                <select name="type_id" id="type_id" class="form-control">
                    <option value="">{{__("Choose Type...")}}</option>
                    @foreach ($types as $types)
                        <option value="{{ $types->id }}">{{ $types->name }}</option>
                    @endforeach
                </select>
                <p class="invalid-feedback"></p>
            </div>
        </div>
        <div class="form-group vendor-name">
        </div>
        <div class="form-group categories">
        </div>
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">{{__('Put File')}}</label>
            <div class="col-sm-10">

                <input required type="file" class="form-control " name="products_file" >
                <p class="invalid-feedback"></p>
            </div>

        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <input id="btn-submit" value="{{__('Add')}}" type="submit" class="btn btn-primary" >
                <a href="{{url('/storage/app/public/samples/products.xlsx')}}" class="btn btn-primary"  download >{{__('Download sample')}} </a>
            </div>
            <div class="col-sm-offset-2 col-sm-10">
            </div>
        </div>
    </form>
@endsection
@section('js')
    <script>$lang = "{{app()->getLocale()}}"</script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                            <select name="vendor_id" id="vendor_id" class="form-control  js-example-basic-single ">
                                <option value="">Choose  Vendor...</option>
                                ${options}
                        </select>
                            <p class="invalid-feedback"></p>
                            </div>
`);
                        }else{
                            console.log('object');
                            $('.vendor-name').append(`
                    <label  class="col-sm-2 control-label" for="" >نوع المورد</label>
                        <div class="col-sm-10">
                            <select name="vendor_id" id="vendor_id" class="form-control js-example-basic-single ">
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
                            <select name="category_id" id="category_id" class="form-control  js-example-basic-single">
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
                            <select name="category_id" id="category_id" class="form-control js-example-basic-single ">
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
        $("#btn-submit").on('click', function(e){
            e.preventDefault();
            var $this = $(this).closest('form');
            fail = true;
            http.checkRequiredFelids($this);
            if(!fail){
                return true;
            }
            var formdata=new FormData($this[0]);
            var buttonText = $this.find('button:submit').text();
            $this.find("button:submit").attr('disabled', true);
            $this.find("button:submit").html('<span class="fas fa-spinner" data-fa-transform="shrink-3"></span>');

            $.ajax({
                enctype: 'multipart/form-data',
                url :$("meta[name='BASE_URL']").attr("content") + "/admin/products/import",
                data : formdata,
                contentType : false,
                processData : false,
                cache : false,
                dataType : 'json',
                type : 'post'
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

@endsection
