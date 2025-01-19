<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\DB;


class CardController extends Controller
{
    // Function create
    public function create(){
        $id = DB::table('cards')->insertGetId([
            'balance' => 0,
        ]);
        return response()->json([
            'id' => $id,
            'message' => 'Berhasil'], 200);
    }

    // Function List
    public function list(Request $request){
        $page = $request->input('page', 0);
        $page_size = $request->input('page_size', 10);
        return reponse()->json([
            'message' => 'Berhasil',
            'cards' => Card::skip($page * $page_size)->take($page_size)->select('id')->get(),
        ], 200);
    }
}
