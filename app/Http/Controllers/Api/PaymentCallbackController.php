<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    // Fungsi menerima data
    public function receive(Request $request){
        $callback = new CallbackService;

        if ($callback->_isSignatureKeyVerified()){
            $notification = $callback->getNotification();
            $order = $callback->getOrder();

            if ($callback->isSuccess()){
                Transaksi::where('id', $order->id)->update([
                    'status_transaksi' => 'TERBAYAR',
                ]);
            }

            if ($callback->isExpire()){
                Transaksi::find($order->id)->delete();
            }

            if ($callback->isCancelled()){
                Transaksi::find($order->id)->delete();
            }
            return response()
                ->json([
                    'success' => true,
                    'message' => 'Notification successfully processed',
                ]);
        }else{
            return response()
                ->json([
                    'error' => true,
                    'message' => 'Signature key not varified',
                ], 403);
        }
    }
}
