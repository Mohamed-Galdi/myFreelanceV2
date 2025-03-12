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
            ->withQueryString();

        $totalClients = Client::count();

        $searchTerm = $request->get('search');

        return inertia('Clients/Index', [
            'clients' => $clients,
            'totalClients' => $totalClients,
            'searchTerm' => $searchTerm
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
