<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Work;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Get summary statistics
        $clientCount = Client::count();
        $projectCount = Project::count();
        $workCount = Work::count();

        $moneyReceived = Payment::sum('amount');
        $moneyPending = Work::sum('price') - Payment::sum('amount');
        $totalEarnings = Work::sum('price');

        $recentWorks = Work::with('project.client')->latest()->take(5)->get()->map(function ($work) {
            return [
                'id' => $work->id,
                'client' => $work->project->client->name,
                'client_id' => $work->project->client->id,
                'project' => $work->project->title,
                'project_id' => $work->project->id,
                'logo' => $work->project->logo,
                'title' => $work->description,
                'price' => $work->price,
                'paid' => $work->getReceivedAmount(),
                'startDate' => $work->start_date,
                'endDate' => $work->end_date,
                'status' => $work->status,


            ];
        });

       

        return Inertia::render('Dashboard', [
            'summary' => [
                'clientCount' => $clientCount,
                'projectCount' => $projectCount,
                'workCount' => $workCount,
                'moneyReceived' => $moneyReceived,
                'moneyPending' => $moneyPending,
                'totalEarnings' => $totalEarnings,
            ],

            'chartData' => [
                'monthly' => $this->getChartData('monthly', 12),
                'weekly' => $this->getChartData('weekly', 12),
                'daily' => $this->getChartData('daily', 30),
                'alltime' => $this->getChartData('alltime'),
            ],

            'recentWorks' => $recentWorks,
        ]);
    }

    private function getChartData($interval, $limit = null)
    {
        $data = [];
        $cumulativeRevenue = 0;

        switch ($interval) {
            case 'daily':
                $startDate = Carbon::now()->subDays($limit);
                $format = 'Y-m-d';
                $periodMethod = 'addDay';
                break;
            case 'weekly':
                $startDate = Carbon::now()->startOfWeek()->subWeeks($limit);
                $format = 'W';
                $periodMethod = 'addWeek';
                break;
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth()->subMonths($limit);
                $format = 'Y-m';
                $periodMethod = 'addMonth';
                break;
            case 'alltime':
                $oldestPayment = Payment::orderBy('payment_date')->first();
                $startDate = $oldestPayment ? Carbon::parse($oldestPayment->payment_date) : Carbon::now();
                $format = 'Y-m';
                $periodMethod = 'addMonth';
                break;
        }

        // Get payments with work and project information
        $payments = Payment::with(['work.project.client'])
            ->where('payment_date', '>=', $startDate)
            ->get();

        // Group payments by period
        $groupedPayments = $payments->groupBy(fn($payment) => Carbon::parse($payment->payment_date)->format($format));

        $currentDate = clone $startDate;
        $endDate = Carbon::now();

        while ($currentDate <= $endDate) {
            $key = $currentDate->format($format);
            $label = match ($interval) {
                'daily' => $currentDate->format('M d'),
                'weekly' => 'Week ' . $currentDate->format('W'),
                default => $currentDate->format('M Y'),
            };

            $periodPayments = $groupedPayments->get($key, collect([]));
            $periodRevenue = $periodPayments->sum('amount');
            $cumulativeRevenue += $periodRevenue;

            // Create payment details for the chart
            $paymentDetails = $periodPayments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount,
                    'date' => $payment->payment_date,
                    'workId' => $payment->work_id,
                    'workDescription' => $payment->work->description,
                    'workStatus' => $payment->work->status,
                    'projectId' => $payment->work->project->id,
                    'projectTitle' => $payment->work->project->title,
                    'clientId' => $payment->work->project->client->id,
                    'clientName' => $payment->work->project->client->name
                ];
            });

            $data[] = [
                'period' => $label,
                'revenue' => $periodRevenue,
                'cumulativeRevenue' => $cumulativeRevenue,
                'payments' => $paymentDetails,
                'paymentCount' => $periodPayments->count(),
            ];

            $currentDate->{$periodMethod}();
        }

        return $data;
    }
}
