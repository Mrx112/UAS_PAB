<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // MidtransNotification
    public function midtransNotification(Request $request)
    {
        // Log request untuk debug
        \Log::info('Midtrans Notification:', $request->all());

        // Proses notifikasi
        // Validasi signature key jika perlu
        // Tambahkan logika sesuai kebutuhan
        return response()->json(['message' => 'Notification received successfully']);
    }
}
