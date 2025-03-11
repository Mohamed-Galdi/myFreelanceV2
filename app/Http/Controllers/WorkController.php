<?php

namespace App\Http\Controllers;

use App\Models\Work;
use Illuminate\Http\Request;


class WorkController extends Controller
{
    public function index()
    {
        $works = Work::with(['project.client', 'payments']) // Include payments
            ->orderBy('created_at', 'desc')
            ->paginate(9)
            ->withQueryString();

        $totalWorks = Work::count();
        $totalRevenue = Work::where('payment_status', 'paid')->sum('price');
        $pendingRevenue = Work::where('payment_status', 'pending')->sum('price');

        return inertia('Works/Index', [
            'works' => $works,
            'totalWorks' => $totalWorks,
            'totalRevenue' => $totalRevenue,
            'pendingRevenue' => $pendingRevenue
        ]);
    }


    public function show(Work $work)
    {
        $work->load('project.client');

        return inertia('Works/Show', [
            'work' => $work
        ]);
    }
}
