<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    // Ongkir

    public function get_ongkir(Request $request)
    {
        $weight = $request->get('weight', 0);
        $origin = env('RAJAONGKIR_ORIGIN');
        $destination = $request->get('destination', 0);
        $courier = $request->get('courier', '');
        $raja_ongkir = Libs::hitung_ongkos_kirim($weight, $origin, $destination
            , $courier);
        if($raja_ongkir['code'] == '200'){
            return response()->json(['services' => $raja_ongkir['services']]
            , 200);
        }else{
            return response()->json(['text' => $raja_ongkir['text']], 500);
        }
    }

    public function checkout(){
        $keranjang = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '')->where('user_id', Auth::user()->id)->first();
        $unpaid = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '<>', '')->where('user_id', Auth::user()->id)->first();
        if($unpaid!=null) return redirect('/transaksi/bayar');
        if($keranjang==null) return redirect('/transaksi/daftar_produk');
        $keranjang->courier = 'pos';
        $alamat = Alamat::find($keranjang->alamat_id);
        $raja_ongkir = Libs::hitung_ongkos_kirim($keranjang->weight,
            env('RAJAONGKIR_ORIGIN'), $alamat->kota_id, $keranjang->courier);
        if($raja_ongkir['code'] == '200'){
            $services = $raja_ongkir['services'];
            $pilihan = $services[0];
            $keranjang->service = $pilihan['service'];
            $keranjang->ongkos_kirim = $pilihan['ongkos_kirim'];
            $keranjang->total_harga = $keranjang->harga_barang + $keranjang->ongkos_kirim;
        }
        return view('transaksi.checkout', ['transaksi' => $keranjang
            , 'destination' => $alamat->kota_id, 'couriers' => ['jne', 'pos', 'tiki']
            , 'services' => $services]);
    }
    
    public function simpan_ongkir(Request $request){
        $request->validate([
            'service' => 'required',
            'courier' => 'required',
            'ongkos_kirim' => 'required|integer',
            'total_harga' => 'required|integer',
        ]);
        $transaksi = Transaksi::find($request->id);
        $transaksi->service = $request->service;
        $transaksi->courier = $request->courier;
        $transaksi->ongkos_kirim = $request->ongkos_kirim;
        $transaksi->total_harga = $request->total_harga;
        $transaksi->save();
        return redirect('/transaksi/bayar');
    }

    public function bayar(){
        $keranjang = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '')->where('user_id', Auth::user()->id)->first();
        $unpaid = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '<>', '')->where('user_id', Auth::user()->id)->first();
        if($keranjang!=null) return redirect('/transaksi/keranjang');
        if($keranjang==null && $unpaid==null) return redirect('/home');
        $order_id = Carbon::parse($unpaid->tanggal_order)
            ->format('Y-m-d').
            str_pad($unpaid->id, 4, '0',STR_PAD_LEFT);
        $resp = Libs::status_midtrans($order_id);
        $token = '';
        $is_expired = 0;
        if($resp['code']=='200'){
            if($resp['message']=='expire' || $resp['message']=='cancel'){
                $unpaid->delete();
                return redirect('/home')->with('status_message',
                    ['type' => 'into',
                    'text' => 'Transaksi dihapus karena gagal bayar!']);
            } else if($resp['message']=='pending'){
                return redirect('/home')->with('status_message',
                    ['type' => 'info',
                    'text' => 'Harap bayar transaksi anda!']);
            }
        }
        $service = new CreateSnapTokenService($unpaid);
        $token = $service->getSnapToken();
        return view('transaksi.bayar', ['transaksi' => $unpaid, 'token' => $token]);
    }
}