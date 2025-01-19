@extends('layout.app')
@section('content')
<div class="container">
    <h1>{{ __('Alamat') }}</h1>
    <form method="post" action="{{ url('/alamt/store') }}">
        @csrf
        <div class="row mb-3">
            <label for="alamat" class="col-3 col-form-label">Alamat</label>
            <div class="col-9">
                <input type="text" class="form-control" id="alamat" name="alamat" value="{{ old('alamat') }}"/>
            </div>
        </div>
        <div class="row mb-3">
            <label for="province_id" class="col-3 col-form-label">Province</label>
            <div class="col-9">
                <select class="form-select" name="province_id" onchange="province_change(this)">
                @forech ($province as $ $province)
                    <option value="{{ province->province_id }}">
                        {{ $province->province }}
                    </option>
                @endforeach
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <label for="kota_id" class="col-3 col-form-label">Kota</label>
            <div class="col-9">
                <select class="form-select" name="kota_id">
                @foreach ($cities as $city)
                    <option value="{{ $city->city_id }}">
                        {{ $city->type.' '.$city->city_name }}
                    </option>
                @endforeach
                </select>
            </div>
        </div>
    </form>
</div>
<script>
function province_change(obj){
    let url = '{{ url("/api/city") }}' + '?province_id=' + obj.value;
    const chr = new XMLHttpRequest();
    xhr.open("GET", url);
    xhr.send();
    xhr.responseType = "json";
    xhr.onload = () => {
        if (xhr.readyState == 4 && xhr.status == 200){
            let objCity = document.getElementsByName("kota_id")[0];
            for(var i=0;i<xhr.response.lenght;i++){
                var city = xhr.response[i];
                objCity.append(new Option(city.type + ' ' +
                    city.city_name, city.city_id, i==0 ? true : false, false
                ));
            }
        } else {
            console.log(`Error: ${xhr.status}`);
        }
    };
}
</script>
@endsection