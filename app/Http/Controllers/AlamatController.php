<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlamatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Keterangan Alamat
        $alamats = Alamat::paginate(10);
        return view('alamat.index', ['alamats' => $alamats]);
    }

    /** 
     * Synchronize Data with API
     * 
     * @return \Illuminate\Http\Response
     */
    public function sync_province()
    {
        $err_message = '';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER => array(
                "key: ".env('RAJAONGKIR_KEY'),
            ),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://api.rajaongkir.com/stater/province',
            CURLOPT_POST => false,
        ));

        $resp = curl_exec($curl);
        if(!$resp){
            $err_message = 'Error: "'.curl_error($curl).'" - code'.curl_errno($curl);
        }

        curl_close($curl);
        if($err_message == ''){
            $json = json_decede($resp, TRUE);
            $provinces = Provinces::get();
            foreach($provinces as $province){
                $province->delete();
            }
            foreach($json['rajaongkir']['results'] as $result){
                Province::create($result);
            }
            return redirect('/alamat')->with('status_message',
                ['type' => 'info', 'text' => 'Province synced!']);
        }else{
            return redirect('/alamat')->with('status_message',
                ['type' => 'danger', 'text' => $err_message]);
        }
    }

    /**
     * Synchornize City to Display on Site
     * 
     * @return \Illuminate\Http\Response
     */
    public function sync_city()
    {
        $err_message = '';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER => array(
                "key: ".env('RAJAONGKIR_KEY'),
            ),
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://api.rajaongkir.com/stater/city',
            CURLOPT_POST => false,
        ));
        $resp = curl_exec($curl);
        if(!$resp){
            $err_message = 'Error: "'.curl_error($curl).'" - Code: '.curl_errno($curl);
        }
        curl_close($curl);
        if($err_message == ''){
            $json = json_decode($resp, TRUE);
            $cities = City::get();
            foreach($cities as $city){
                $city->delete();
            }
            foreach($json['rajaongkir']['results'] as $result){
                City::create($result);
            }
            return redirect('/alamat')->with('status_message',
                ['type' => 'info', 'text' => 'City synced!']);
        }else{
            return redirect('/alamat')->with('status_message',
                ['type' => 'danger', 'text' => $err_message]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Function Create / membuat sebuah data baru
        $province = Province::get();
        $cities = City::where('province_id', $province->first()->province_id)->get();
        return view('alamat.create', ['province' => $province, 'cities' => $cities]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Function Store
        $request->validate([
            'alamat' => 'required|max:255',
            'kota_id' => 'required|exists:cities,city_id',
        ]);
        $data = $request->all();
        $data['province_id'] = City::where('city_id', $data['kota_id'])->first()->province_id;
        $data['user_id'] = Auth::user()->id;
        Alamat::create($data);
        return redirect('/home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Function show / display
        $alamat = Alamat::where('user_id', Auth::user()->id)->first();
        return view('alamat.show', ['alamat' => $alamat]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Edit
        if($id==0){
            $alamat = Alamat::where('user_id', Auth::user()->id)->first();
        }else{
            $alamat = Alamat::find($id);
        }
        $province = Province::get();
        $cities = City::where('province_id', $alamat->province_id)->get();
        return view('alamat.edit', ['alamat' => $alamat, 'province' => $provinces, 'cities' => $cities]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Update data
        $request->validate([
            'alamat' => 'required|max:255',
            'kota_id' => 'required|exists:cities,city_id',
        ]);
        $data = $request->all();
        $data['province_id'] = City::where('city_id', $data['kota_id'])->first()->province_id;
        $alamat = Alamat::find($id);
        $alamat->update($data);
        if(Auth::user()->role=='KONSUMEN'){
            return redirect('/home');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // menghapus Data
        $alamat = Alamat::find($id);
        $alamat->delete();
        return redirect('/alamat');
    }
}
