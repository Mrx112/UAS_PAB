<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\Terminal;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TerminalController extends Controller
{
    //Create
    public function create(Request $request){
        $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users'],
            'branch' => ['required', 'string', 'max:255'],
            'is_payment' => ['required', 'boolean'],
            'is_deposit' => ['required', 'boolean'],
        ]);
        $email = $request->email;
        $is_payment = $request->get('is_payment', 0);
        $is_deposit = $request->get('is_deposit', 0);
        if($is_payment==0 && $is_deposit==0){
            return response()->json([
                'message' => 'Is_payment dan is_deposit tidak boleh 0'
            ], 422);
        }
        if($is_payment==1 && $is_deposit==1){
            return response()->json([
                'message' => 'Is_payment dan is_deposit tidak boleh 1'
            ], 422);
        }
        $user = user::where('email', $email)->first();
        $terminal = Terminal::where('user_id', $user->id)->first();
        if($terminal!=null){
            return response()->json([
                'message' => 'Email sudah dipakai di terminal lain'
            ], 422);
        }
        DB::table('terminals')->insert([
            'user_id' => $user->id,
            'branch' => $request->branch,
            'is_payment' => $is_payment,
            'is_deposit' => $is_deposit,
        ]);
        return response()->json(['message' => 'Berhasil'], 200);        
    }

    // Function List
    public function list(Request $request){
        $page = $request->input('page', 0);
        $page_size = $request->input('page_size', 10);
        return response()->json([
            'message' => 'Berhasil',
            'terminals' => Terminal::skip($page * $page_size)->take($page_size)->get(),
        ], 200);
    }
}
