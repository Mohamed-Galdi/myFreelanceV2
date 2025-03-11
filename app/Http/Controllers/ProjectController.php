<?php

namespace App\Http\Controllers;


use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('client')
            ->orderBy('title')
            ->paginate(9)
            ->withQueryString();

        $totalProjects = Project::count();
        $totalRevenue = Project::sum('total_revenue');

        return inertia('Projects/Index', [
            'projects' => $projects,
            'totalProjects' => $totalProjects,
            'totalRevenue' => $totalRevenue
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
}
