<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CityController extends Controller
{
    // Controller to Display and search City or Provinsi
    public function index(Request $request){
        $province_id = $request->get('province_id', 0);
        if(City::where('province_id', $province_id)->exists()){
            $result = City::where('province_id', $province_id)->get()->toArray();
        }else{
            $result = [['city_id' => 0, 'city_name' => 'Harap Pilih Provinsi', 'type' => '', 'postal_code' => '00000']];
        }
    }
}
