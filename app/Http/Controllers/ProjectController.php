<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Services\FileService;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::when($request->search, function ($query) use ($request) {
            $query->where('title', 'like', '%' . $request->search . '%');
        })
            ->with('client')
            ->orderBy('created_at', 'desc')
            ->paginate(9)
            ->withQueryString()
            ->through(function ($project) {
                return [
                    ...$project->toArray(),
                    'total_works' => $project->getTotalWorks(),
                    'total_payments' => $project->getTotalPayments(),
                ];
            });

        $totalProjects = Project::count();

        $clients = Client::select('id', 'name')->get()->toArray();

        $searchTerm = $request->get('search');


        return inertia('Projects/Index', [
            'clients' => $clients,
            'projects' => $projects,
            'totalProjects' => $totalProjects,
            'searchTerm' => $searchTerm
        ]);
    }

    public function show(Project $project)
    {
        $project->load(['client', 'works' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        $project->total_revenue = $project->getTotalRevenue();

        $clients = Client::select('id', 'name')->get()->toArray();

        return inertia('Projects/Show', [
            'project' => $project,
            'clients' => $clients,
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

        return '';
    }

    public function update(Request $request, $projectId)
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

        $project = Project::find($projectId);
        $project->client_id = $request->clientId;
        $project->title = $request->title;
        $project->description = $request->description;
        $project->tech_stack = $request->tech_stack;
        $project->github_repo = $request->github_repo;
        $project->live_link = $request->live_link;

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

        return '';
    }

    public function destroy($projectId)
    {
        $project = Project::findOrFail($projectId);
        $project->delete();

        return redirect()->route('projects');
    }
}
