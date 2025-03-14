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
        $totalRevenue = Payment::sum('amount');

        $firstWorkDate = Work::min('start_date');
        $monthlyAverage = 0;

        if ($firstWorkDate) {
            $monthsSinceFirst = max(1, Carbon::parse($firstWorkDate)->diffInMonths(Carbon::now()));
            $monthlyAverage = $totalRevenue / $monthsSinceFirst;
        }

        $clientCount = Client::count();
        $projectCount = Project::count();
        $workCount = Work::count();

        // Work status counts
        $workStats = [
            'total' => Work::count(),
            'completed' => Work::where('status', 'completed')->count(),
            'ongoing' => Work::where('status', 'ongoing')->count(),
            'cancelled' => Work::where('status', 'cancelled')->count(),
        ];

        // Payment status counts
        $paymentStats = [
            'paid' => 0,
            'pending' => 0,
            'refunded' => 0,
            'cancelled' => 0,
        ];

        // Recent projects
        $recentProjects = Project::with('client')->latest()->take(5)->get();

        // Upcoming and overdue works
        $today = Carbon::today();
        $upcomingWorks = Work::with('project.client')
            ->where('end_date', '>=', $today)
            ->orderBy('end_date')
            ->take(5)
            ->get();

        $overdueWorks = Work::with('project.client')
            ->where('end_date', '<', $today)
            ->orderBy('end_date')
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'summary' => [
                'totalRevenue' => $totalRevenue,
                'monthlyAverage' => $monthlyAverage,
                'clientCount' => $clientCount,
                'projectCount' => $projectCount,
                'workCount' => $workCount,
            ],
            'workStats' => $workStats,
            'paymentStats' => $paymentStats,
            'chartData' => [
                'monthly' => $this->getChartData('monthly', 12),
                'weekly' => $this->getChartData('weekly', 12),
                'daily' => $this->getChartData('daily', 30),
                'alltime' => $this->getChartData('alltime'),
            ],
            'recentProjects' => $recentProjects,
            'upcomingWorks' => $upcomingWorks,
            'overdueWorks' => $overdueWorks,
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
                $oldestWork = Work::orderBy('start_date')->first();
                $startDate = $oldestWork ? Carbon::parse($oldestWork->start_date) : Carbon::now();
                $format = 'Y-m';
                $periodMethod = 'addMonth';
                break;
        }

        $works = Work::where('start_date', '>=', $startDate)
            ->get()
            ->groupBy(fn($work) => Carbon::parse($work->start_date)->format($format));

        $currentDate = clone $startDate;
        $endDate = Carbon::now();

        while ($currentDate <= $endDate) {
            $key = $currentDate->format($format);
            $label = match ($interval) {
                'daily' => $currentDate->format('M d'),
                'weekly' => 'Week ' . $currentDate->format('W'),
                default => $currentDate->format('M Y'),
            };

            $periodWorks = $works->get($key, collect([]));
            $periodRevenue = $periodWorks->where('payment_status', 'paid')->sum('price');
            $cumulativeRevenue += $periodRevenue;

            $data[] = [
                'period' => $label,
                'revenue' => $periodRevenue,
                'cumulativeRevenue' => $cumulativeRevenue,
                'workCount' => $periodWorks->count(),
            ];

            $currentDate->{$periodMethod}();
        }

        return $data;
    }
}
