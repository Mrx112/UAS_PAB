<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    // Daftar Produk
    public function daftar_produk(){
        if(Transaksi::where('status_transaksi', 'PESAN')
            -> where('user_id', Auth::user()->id)
            ->first()!=null) return redirect('/transaksi/keranjang');
        $produks = Produk::paginate(4);
        return view('transaksi.daftar_produk', ['produks' => $produks]);
    }

    public function tambah_keranjang(Request $request){
        $request->validate([
            'qty' => 'required|integer',
            'produk_id' => 'required|exists:produk,id',
        ]);
        $keranjang = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '')->where('user_id', Auth::user()->id)->first();
        $checkedOut = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '<>', '')
            ->where('user_id', Auth::user()->id)->first();
        if($checkedOut!=null) return redirect('/home');
        if($keranjang!=null){
            if($keranjang->produk_id!=$request->produk_id){
                return redirect('/transaksi/keranjang');
            }
        }
        if($keranjang==null){
            $keranjang = new Transaksi();
            $keranjang->tanggal_order = Carbon::today();
            $keranjang->user_id = Auth::user()->id;
            $alamat = Alamat::where('user_id', $keranjang->user_id)->first();
            $keranjang->alamat_id = $alamat->id;
            $keranjang->produk_id = $request->produk_id;
            $keranjang->qty = $request->qty;
            $keranjang->status_transaksi = 'PESAN';
            $keranjang->rating = 1;
            $keranjang->courier = '';
            $keranjang->service = '';
            $keranjang->waktu_kirim = 0;
            $keranjang->ongkos_kirim = 0;
            $keranjang->total_harga = 0;
        }else{
            $keranjang->qty = $request->qty;
        }
        $produk = Produk::find($request->produk_id);
        $keranjang->harga_barang = $produk->harga * $keranjang->qty;
        $berat_kirim = $this->hitung_berat_kirim($keranjang->qty, $produk->berat);
        $keranjang->weight = $berat_kirim;
        $keranjang->save();
        return redirect('/transaksi/keranjang');
    }

    public function hapus_keranjang(Request $request){
        $keranjang = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '')->where('user_id', Auth::user()->id)->first();
        $checkedOut = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '<>', '')->where('user_id', Auth::user()->id)->first();
        if($checkedOut!=null) return redirect('/home');
        if($keranjang!=null){
            $keranjang->delete();
        }
        return redirect('/home');
    }

    public function hitung_berat_kirim($qty,$berat){
        $berat_wadah = 50;
        return ceil((($qty * ($berat + $berat_wadah)))/1000.0)*1000;
    }

    public function keranjang(){
        $keranjang = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '')->where('user_id', Auth::user()->id)->first();
        $checkedOut = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '<>', '')->where('user_id', Auth::user()->id)->first();
        if($checkedOut!=null) return redirect('/home');
        if($keranjang==null) return redirect('/transaksi/daftar_produk');
        return view('transaksi.keranjang', ['transaksi' => $keranjang]);
    }

    public function bayar(){
        $keranjang = Transaksi::where('status_transaksi', 'PESAN')
            ->where('courier', '')->where('user_id', Auth::user()->id)->first();
        $unpaid = Transaksi::where('staus_transaksi', 'PESAN')
            ->where('courier', '<>', '')->where('user_id', Auth::user()->id)->first();
        if($keranjang!=null) return redirect('/transaksi/keranjang');
        if($keranjang==null && $unpaid==null) return redirect('/home');
        $order_id = Carbon::parse($unpaid->tanggal_order)
            ->format('Y-m-d').str_pad($unpaid->id, 4, '0', STR_PAD_LEFT);
        $resp = Libs::status_midtrans($order_id);
        $token = '';
        $is_expired = 0;
        if($resp['code']=='200'){
            if($resp['message']=='expire' || $resp['message']=='cancel'){
                $unpaid->delete();
                return redirect('/home')->with('status_message',
                ['type' => 'info',
                'text' => 'Transaksi dihapus karena gagal bayar!']);
            }else if($resp['message']=='pending'){
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