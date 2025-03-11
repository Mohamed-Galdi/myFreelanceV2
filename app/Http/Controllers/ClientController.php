<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::orderBy('name')
            ->paginate(9)
            ->withQueryString();

        $totalClients = Client::count();

        return inertia('Clients/Index', [
            'clients' => $clients,
            'totalClients' => $totalClients
        ]);
    }

    // ClientController.php (add this method)
    public function show(Client $client)
    {
        // Load all necessary relations for the client details page
        $client->load([
            'projects' => function ($query) {
                $query->withCount('works');
            },
            'projects.works' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        // Calculate additional metrics
        $totalPaidWorks = 0;
        $totalPendingWorks = 0;
        $totalPaidRevenue = 0;
        $totalPendingRevenue = 0;

        foreach ($client->projects as $project) {
            foreach ($project->works as $work) {
                if ($work->payment_status === 'paid') {
                    $totalPaidWorks++;
                    $totalPaidRevenue += $work->price;
                } else if ($work->payment_status === 'pending') {
                    $totalPendingWorks++;
                    $totalPendingRevenue += $work->price;
                }
            }
        }

        $metrics = [
            'totalPaidWorks' => $totalPaidWorks,
            'totalPendingWorks' => $totalPendingWorks,
            'totalPaidRevenue' => $totalPaidRevenue,
            'totalPendingRevenue' => $totalPendingRevenue
        ];

        return inertia('Clients/Show', [
            'client' => $client,
            'metrics' => $metrics
        ]);
    }
}
