<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        // Get paginated payments with related data
        $payments = Payment::with(['work.project.client'])
            ->orderBy('payment_date', 'desc')
            ->paginate(10);

        // Calculate total amount of all payments
        $totalAmount = Payment::sum('amount');

        // Get last payment
        $lastPayment = Payment::orderBy('payment_date', 'desc')->first();

        // Calculate pending payments (work price - payments made)
        $pendingAmount = DB::table('works')
            ->select(DB::raw('SUM(works.price) - COALESCE(SUM(payments.amount), 0) as pending'))
            ->leftJoin('payments', 'works.id', '=', 'payments.work_id')
            ->groupBy('works.id')
            ->havingRaw('SUM(works.price) > COALESCE(SUM(payments.amount), 0)')
            ->get()
            ->sum('pending');

        // Get payments by project
        $projectsData = DB::table('projects')
            ->join('works', 'projects.id', '=', 'works.project_id')
            ->join('payments', 'works.id', '=', 'payments.work_id')
            ->select('projects.id', 'projects.title', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('projects.id', 'projects.title')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Get payments by client
        $clientsData = DB::table('clients')
            ->join('projects', 'clients.id', '=', 'projects.client_id')
            ->join('works', 'projects.id', '=', 'works.project_id')
            ->join('payments', 'works.id', '=', 'payments.work_id')
            ->select('clients.id', 'clients.name', DB::raw('SUM(payments.amount) as total'))
            ->groupBy('clients.id', 'clients.name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $works = Work::select('id', 'description')
            ->with('project')
            ->get()
            ->map(function ($work) {
                return [
                    'id' => $work->id,
                    'name' => $work->description . ' - ' . ($work->project->title ?? 'No Project')
                ];
            })
            ->toArray();

        return inertia('Payments/Index', [
            'payments' => $payments,
            'totalAmount' => $totalAmount,
            'totalPending' => $pendingAmount,
            'lastPayment' => $lastPayment,
            'projectsData' => $projectsData,
            'clientsData' => $clientsData,
            'works' => $works
        ]);
    }

    public function show(Payment $payment)
    {
        // Load the payment with all related data
        $payment->load(['work.project.client']);

        // Calculate how much of the work price has been paid
        $totalPaid = Payment::where('work_id', $payment->work_id)->sum('amount');
        $remainingAmount = $payment->work->price - $totalPaid;

        // Get all payments for the same work
        $relatedPayments = Payment::where('work_id', $payment->work_id)
            ->where('id', '!=', $payment->id)
            ->orderBy('payment_date', 'desc')
            ->get();

        $works = Work::select('id', 'description')
            ->with('project')
            ->get()
            ->map(function ($work) {
                return [
                    'id' => $work->id,
                    'name' => $work->description . ' - ' . ($work->project->title ?? 'No Project')
                ];
            })
            ->toArray();

        return inertia('Payments/Show', [
            'payment' => $payment,
            'totalPaid' => $totalPaid,
            'remainingAmount' => $remainingAmount,
            'relatedPayments' => $relatedPayments,
            'works' => $works
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'workId' => 'required|exists:works,id',
            'amount' => 'required|numeric',
            'paymentDate' => 'required|date',
            'paymentMethod' => 'required|in:Western Union,Bank Transfer,PayPal,Cash,Upwork',
            'note' => '',
        ]);

        $payment = new Payment();
        $payment->work_id = $request->workId;
        $payment->amount = $request->amount;
        $payment->payment_date = $request->paymentDate;
        $payment->payment_method = $request->paymentMethod;
        $payment->note = $request->note;
        $payment->save();

        return '';
    }

    public function update(Request $request, $paymentId)
    {
        $request->validate([
            'workId' => 'required|exists:works,id',
            'amount' => 'required|numeric',
            'paymentDate' => 'required|date',
            'paymentMethod' => 'required|in:Western Union,Bank Transfer,PayPal,Cash,Upwork',
            'note' => '',
        ]);

        $payment = Payment::findOrFail($paymentId);
        $payment->work_id = $request->workId;
        $payment->amount = $request->amount;
        $payment->payment_date = $request->paymentDate;
        $payment->payment_method = $request->paymentMethod;
        $payment->note = $request->note;
        $payment->save();

        return '';
    }

    public function destroy($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->delete();
        return redirect()->route('payments');
    }
}
