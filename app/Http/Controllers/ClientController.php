<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = Client::when($request->search, function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->search . '%');
        })
            ->orderBy('created_at', 'desc')
            ->paginate(9)
            ->withQueryString()
            ->through(function ($client) {
                return [
                    ...$client->toArray(),
                    'total_projects' => $client->getTotalProjects(),
                    'total_revenue' => $client->getTotalRevenue(),
                    'pending_amount' => $client->getPendingAmounts(),
                ];
            });
            ;

        $totalClients = Client::count();

        $searchTerm = $request->get('search');

        return inertia('Clients/Index', [
            'clients' => $clients,
            'totalClients' => $totalClients,
            'searchTerm' => $searchTerm
        ]);
    }

    public function show(Client $client)
    {
        // First load the relationships
        $client->load([
            'projects' => function ($query) {
                $query->withCount('works')->orderBy('created_at', 'desc');
            },
            'projects.works' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'projects.works.payments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
        ]);

        $client->projects = $client->projects->map(function ($project) {
            // First add total_revenue to the project
            $project->total_revenue = $project->getTotalRevenue();

            // Then add remaining_amount to each work
            $project->works = $project->works->map(function ($work) {
                $work->remaining_amount = $work->getRemainingAmount();
                return $work;
            });

            return $project;
        });


        $metrics = [
            'totalProjects' => $client->getTotalProjects(),
            'totalWorks' => $client->getTotalWorks(),
            'totalRevenue' => $client->getTotalRevenue(),
            'pendingAmount' => $client->getPendingAmounts(),
        ];

        return inertia('Clients/Show', [
            'client' => $client,
            'metrics' => $metrics
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['required', 'string', 'max:255'],
            'source' => ['required', 'string', 'max:255'],
        ]);
        Client::create([
            'name' => $request->name,
            'contact' => $request->contact,
            'source' => $request->source,
        ]);
        return '';
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['required', 'string', 'max:255'],
            'source' => ['required', 'string', 'max:255'],
        ]);

        $client = Client::findOrFail($id);
        $client->name = $request->name;
        $client->contact = $request->contact;
        $client->source = $request->source;
        $client->save();

        return '';
    }

    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return redirect()->route('clients');
    }
}
