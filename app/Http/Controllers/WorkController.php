<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Work;
use Carbon\Carbon;
use Illuminate\Http\Request;


class WorkController extends Controller
{
    public function index()
    {
        $works = Work::with(['project.client', 'payments']) // Include payments
            ->orderBy('created_at', 'desc')
            ->paginate(9)
            ->withQueryString()
            ->through(function ($work) {
                return [
                    ...$work->toArray(),
                    'paid' => $work->getReceivedAmount(),
                ];
            });

        $projects = Project::select('id', 'title')->get()->toArray();

        $totalWorks = Work::count();

        return inertia('Works/Index', [
            'works' => $works,
            'totalWorks' => $totalWorks,
            'projects' => $projects
        ]);
    }


    public function show(Work $work)
    {
        $work->load('project.client', 'payments');

        $work->paid = $work->getReceivedAmount();

        $projects = Project::select('id', 'title')->get()->toArray();

        $worksOfSameProject = Work::where('project_id', $work->project_id)
            ->where('id', '!=', $work->id)
            ->get() // Fetch results first
            ->map(function ($work) {
                return array_merge($work->toArray(), [
                    'paid' => $work->getReceivedAmount(),
                ]);
            });

        return inertia('Works/Show', [
            'work' => $work,
            'projects' => $projects,
            'worksOfSameProject' => $worksOfSameProject
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'projectId' => 'required|exists:projects,id',
            'description' => 'required',
            'price' => 'required|numeric',
            'startDate' => 'required|date',
            'endDate' => '',
        ]);

        $work = new Work();
        $work->project_id = $request->projectId;
        $work->description = $request->description;
        $work->price = $request->price;
        $work->start_date = Carbon::parse($request->startDate)->toDateString();
        $work->end_date = $request->endDate ? Carbon::parse($request->endDate)->toDateString() : null;
        $work->save();

        return '';
    }

    public function update(Request $request, $workId)
    {
        $request->validate([
            'projectId' => 'required|exists:projects,id',
            'description' => 'required',
            'price' => 'required|numeric',
            'startDate' => 'required|date',
            'endDate' => '',
            'status' => 'required|in:Ongoing,Completed,Cancelled',
        ]);

        $work = Work::findOrFail($workId);
        $work->project_id = $request->projectId;
        $work->description = $request->description;
        $work->price = $request->price;
        $work->start_date = Carbon::parse($request->startDate)->toDateString();
        $work->end_date = $request->endDate ? Carbon::parse($request->endDate)->toDateString() : null;
        $work->status = $request->status;
        $work->save();

        return '';
    }

    public function destroy($workId)
    {
        $work = Work::findOrFail($workId);
        $work->delete();

        return redirect()->route('works');
    }
}
