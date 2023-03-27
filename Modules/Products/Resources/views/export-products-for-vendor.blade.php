@extends('layouts.app')
@section('content')
    <form id="target" action="{{route('products.export')}}" method="get"  enctype="multipart/form-data" class="form-horizontal">
        @csrf
        <div class="form-group">
            <div class="col-sm-10" style="margin-left: 50px">
                <h1>{{__('Exporting Excel')}}</h1>
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
            <div class="col-sm-offset-2 col-sm-10">
                <input id="btn-submit" value="{{__('Export')}}" type="submit" class="btn btn-primary" >
            </div>
        </div>
    </form>
@endsection
