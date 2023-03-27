@extends('layouts.app')
@section('content')
@if (\Auth::user()->hasRole('super_admin'))
<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container">
        <!--begin::Card-->
        <div class="gutter-b example example-compact">

            <div class="contentTabel">
                <button  type="button" class="btn btn-secondar btn--filter mr-2"><i class="icon-xl la la-sliders-h"></i>{{__('filter')}}</button>
                <div class="container box-filter-collapse" >
                    <div class="card" >
                        <div class="col-md">
                            <div class="form-group">
                                <label class="control-label">{{__('Vendors')}}</label>
                                <select id="vendor_id" class="form-control js-example-basic-single"name="vendor_id">
                                    <option value="">{{__('Choose Type...')}}</option>
                                    @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Card-->
    </div>
    <!--end::Container-->
</div>

@endif
   
<div class="content">
    <table class="table table-bordered data-table" style="width: 100%">
        <thead>
            <tr>
                <th></th>
                <th>#</th>
                <th>{{__('Vendor Name')}}</th>
                <th>{{__('User Name')}}</th>
                <th>{{__('Drvier Name')}}</th>
                <th>{{__('Total')}}</th>
                <th>{{__('Total Afer Discount')}}</th>
                <th>{{__('Last State')}}</th>
                <th>{{__('Created At')}}</th>
                <th width="100px">{{__('Actions')}}</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
@endsection
@section('js')
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
<script>
    $(function () {
        var table = $('.data-table').DataTable({
        searching: false,
          processing: true,
          serverSide: true,
          dom: '<"dt-top-container"<B><"dt-center-in-div"l><f>r>t<"dt-filter-spacer"><ip>',

          ajax: {
            url:  "{{ route('orders.manage') }}",
            data: function (d) {
                d.vendor_id = $('select[name="vendor_id"]').val()
            }

          },
          columns: [
            {
                className: 'dt-control',
                orderable: false,
                data: null,
                defaultContent: '',
            },
              {data: 'id', name: 'id'},
              {data: 'vendor.company_name', name: 'vendor.company_name', defaultContent: "__"},
              {data: 'user.first_name', name: 'user.first_name', defaultContent: "__"},
              {data: 'driver.user.full_name', name: 'driver.user.full_name', defaultContent: "__"},
              {data: 'total', name: 'total', defaultContent: "__"},
              {data: 'after_discount', name: 'after_discount', defaultContent: "__"},
              {data: 'last_status.state.name.en', name: 'last_status.state.name.en', defaultContent: "__"},
              {data: 'created_at', name: 'created_at', defaultContent: "__"},
              {data: 'action', name: 'action', orderable: false, searchable: false , defaultContent: "__"},

          ],
        });
        changeStatusForAdmin('orders');
        
        setTimeout(() => {
            $('.data-table tbody').on('click', 'td.dt-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
    
            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                row.child(format(row.data().order_details,row.data())).show();
                tr.addClass('shown');
            }
        });
        }, 1000);
        function format(d,data1) {
            data = '';
            result = '';
            if(d != null){
                d.forEach(element => {
                    console.log((element.quantity) * (element.price));
                    data += '<tr>';
                        data += `<td> ${element.product.product_code} </td>`;        
                        data += `<td> ${element.product.name.en} </td>` ;       
                        data += `<td> ${element.variation.attributes[0] ? element.variation.attributes[0].value : "-"}  </td>` ;       
                        data += `<td> ${element.variation.attributes[1] ? element.variation.attributes[1].value : "-"} </td>` ;       
                        data += `<td> ${element.quantity} </td>` ;       
                        data += `<td> ${element.price} </td>`  ;      
                        data += `<td> ${(element.quantity) * (element.price)} </td>`  ;      
                    data += '</tr>';
                });
                result += `
                <table class="table table-bordered" style="width: 100%">
                    <thead>
                        <tr>
                            <th>{{__('Product Code')}}</th>
                            <th>{{__('Product Name')}}</th>
                            <th>{{__('Attribute 1')}}</th>
                            <th>{{__('Attribute 2')}}</th>
                            <th>{{__('Quantity')}}</th>
                            <th>{{__('Price')}}</th>
                            <th>{{__('Total')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                       ${data}
                    </tbody>
                </table>
                `;
            }
            if(data1.coupon != null){
                result += `<h3>Coupons</h3>
                    <table class="table table-bordered" style="width: 100%">
                        <thead>
                            <tr>
                                <th>{{__('Id')}}</th>
                                <th>{{__('Code')}}</th>
                                <th>{{__('name')}}</th>
                                <th>{{__('value')}}</th>
                                <th>{{__('amount(condition)')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        <td>${data1.coupon.coupon.id}</td>
                        <td>${data1.coupon.coupon.code}</td>
                        <td>${data1.coupon.coupon.name.en}</td>
                        <td>${data1.coupon.coupon.value}</td>
                        <td>${data1.coupon.coupon.amount}</td>
                        </tbody>
                    </table>`;
            }
            if(data1.offer != null){
                result += `<h3>Offers</h3>
                    <table class="table table-bordered" style="width: 100%">
                        <thead>
                            <tr>
                                <th>{{__('Id')}}</th>
                                <th>{{__('type')}}</th>
                                <th>{{__('name')}}</th>
                                <th>{{__('value')}}</th>
                                <th>{{__('amount(condition)')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        <td>${data1.offer.offer.id}</td>
                        <td>${data1.offer.offer.type.name.en}</td>
                        <td>${data1.offer.offer.name.en}</td>
                        <td>${data1.offer.offer.value}</td>
                        <td>${data1.offer.offer.amount}</td>
                        </tbody>
                    </table>`;
            }
            if(data1.add_status != null){
                data2 ='<h3>Order State</h3>';
                data1.add_status.forEach(element => {
                    data2 += '<tr>';
                        data2 += `<td> ${element.id} </td>`;        
                        data2 += `<td> ${element.state.name.en} </td>` ;       
                        data2 += `<td> ${element.updated_at} </td>`  ;      
                    data2 += '</tr>';
                });
                result += `
                <table class="table table-bordered" style="width: 100%">
                    <thead>
                        <tr>
                            <th>{{__('#')}}</th>
                            <th>{{__('State Name')}}</th>
                            <th>{{__('Time')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                       ${data2}
                    </tbody>
                </table>
                `;
            }
            return result;
        }
        $('input').on('change', function(e) {
            table.draw();
            e.preventDefault();
        });
        $('select').on('change', function(e) {
            table.draw();
            e.preventDefault();
        });
       
    });
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
    });

  </script>
  
@endsection
