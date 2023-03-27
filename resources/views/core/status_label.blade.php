{{-- {{dd("dd")}} --}}
<td class="v-align-middle wd-10p" > <span id="label-{{$row->id}}" class="badge badge-pill badge-{{($row->status_id == "1") ? "info" : "danger"}}" id="label-{{$row->id}}">

    {{$row->status_id == "1" ? 'active' : "not active"}}
</span>
</td>