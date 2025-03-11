<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('work')->orderBy('payment_date', 'desc')->paginate(9);

        return inertia('Payments/Index', [
            'payments' => $payments
        ]);
    }

    public function show(Payment $payment)
    {
        return inertia('Payments/Show', [
            'payment' => $payment
        ]);
    }
}
