@extends('layouts.app')
@section('content')
<div class="container">
    <h1>{{ __('Produk') }}</h1>
    <div class="col">
            <div class="card">
                    <img src="{{ url('/produk.jpg') }}" class="card-img-top" style="height:300px;object-fit:cover">
                    <div class="card-body">
                        <h5 class="card-title">{{ $produk->nama_produk }}</h5>
                        <p class="card-text">
                            IDR {{ number_format($produk->harga) }}</p>
                    </div>
                    <div class="card-body text-end">
                        <form action="{{ url('transaksi/tambah_keranjang') }}" method="post">
                            @csrf
                            <input type="hidden" name="qty" value="1"/>
                            <input type="hidden" name="produk_id" valur="{{ $produk->id }}"/>
                            <button type="submit" class="btn btn-primary"> Keranjang </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            @endforelse
        </div>
        <br/>
    @if($produks)
    {{ $produks->links() }}
    @endif
    </div>
    @endsection