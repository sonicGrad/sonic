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
            <label for="" class="col-sm-2 control-label">{{__('Put File')}}</label>
            <div class="col-sm-10">

                <input required type="file" class="form-control " name="products_file" >
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
