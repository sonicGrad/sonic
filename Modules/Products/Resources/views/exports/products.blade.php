<table>
    <thead>
    <tr>
        <th style="background: rgb(227, 219, 10)">#</th>
        <th style="background: rgb(227, 219, 10)">{{__('Product Code')}}</th>
        <th style="background: rgb(227, 219, 10)">{{__('Name')}}</th>
        <th style="background: rgb(227, 219, 10)">{{__('Description')}}</th>
        <th style="background: rgb(227, 219, 10)">{{__('Category')}}</th>
        <th style="background: rgb(227, 219, 10)">{{__('Vendor Name')}}</th>
        <th style="background: rgb(227, 219, 10)">{{__('Price')}}</th>
        <th style="background: rgb(227, 219, 10)">{{__('Created At')}}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        <tr>
            <td >{{$product->id}}</td>
            <td style="width: 100px">{{$product->product_code}}</td>
            <td style="width: 100px">{{ $product->name }}</td>
            <td style="width: 200px">{{ $product->description }}</td>
            <td style="width: 100px">{{ $product->category->name }}</td>
            <td style="width: 100px">{{ $product->vendor->company_name }}</td>
            <td style="width: 100px">{{ $product->price  }}</td>
            <td style="width: 200px">{{ $product->created_at  }}</td>
        </tr>
    @endforeach
    </tbody>
</table>