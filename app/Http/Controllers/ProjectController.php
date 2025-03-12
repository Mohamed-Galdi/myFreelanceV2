<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Services\FileService;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('client')
            ->orderBy('created_at', 'desc')
            ->paginate(6)
            ->withQueryString();

        $totalProjects = Project::count();

        $clients = Client::select('id', 'name')->get()->toArray();

        return inertia('Projects/Index', [
            'clients' => $clients,
            'projects' => $projects,
            'totalProjects' => $totalProjects,
        ]);
    }

    public function show(Project $project)
    {
        $project->load(['client', 'works' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        // Calculate some additional stats
        $ongoingWorks = $project->works->where('project_status', 'ongoing')->count();
        $completedWorks = $project->works->where('project_status', 'completed')->count();
        $pendingPayments = $project->works->where('payment_status', 'pending')->sum('price');

        return inertia('Projects/Show', [
            'project' => $project,
            'stats' => [
                'ongoingWorks' => $ongoingWorks,
                'completedWorks' => $completedWorks,
                'pendingPayments' => $pendingPayments
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'clientId' => 'required|exists:clients,id',
            'title' => 'required',
            'description' => '',
            'logo' => '',
            'image' => '',
            'tech_stack' => 'required',
            'github_repo' => '',
            'live_link' => '',
        ]);

        $project = new Project();
        $project->client_id = $request->clientId;
        $project->title = $request->title;
        $project->description = $request->description;
        $project->tech_stack = $request->tech_stack;
        $project->github_repo = $request->github_repo;
        $project->live_link = $request->live_link;
        $project->status = 'ongoing';
        
        // Move logo if exists
        if ($request->has('logo') && $request->logo) {
            $project->logo = FileService::moveTempFile($request->logo, "projects/logos/{$project->id}", $project->title);
            $project->save();
        }
        
        // Move image if exists
        if ($request->has('image') && $request->image) {
            $project->image = FileService::moveTempFile($request->image, "projects/images/{$project->id}", $project->title);
            $project->save();
        }
        $project->save();
        
        $client = Client::find($request->clientId);
        $client->projects_count++;
        $client->save();

        return '';
    }
}
