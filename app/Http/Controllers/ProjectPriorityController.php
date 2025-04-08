<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProjectPriorities;
use App\Models\Project;

class ProjectPriorityController extends Controller
{
    public function index(){
        $projectPriorities = ProjectPriorities::all();
        return view('project_priorities.index', compact('projectPriorities'));
    }
    public function create($projectId){
        $project = Project::findOrFail($projectId);
        return view('project_priorities.create', compact('project'));
    }
    public function store(Request $request){
        $request->validate([
            'id_project' => 'required|exists:projects,id',
            'name' => 'required|string|max:100',
            'kind' => 'required|integer',
            'execution_time_limit' => 'required|integer',
        ]);

        $projectPriority = ProjectPriorities::create($request->all());
        return redirect()->route('projects.show', $projectPriority->id_project)
            ->with('success', 'Priorita byla úspěšně vytvořena.');
    }
    public function edit($id){
        $projectPriority = ProjectPriorities::findOrFail($id);
        $projects = Project::all();
        return view('project_priorities.edit', compact('projectPriority', 'projects'));
    }
    public function update(Request $request, $id){
        $request->validate([
            'id_project' => 'required|exists:projects,id',
            'name' => 'required|string|max:100',
            'kind' => 'required|integer',
            'execution_time_limit' => 'required|integer',
        ]);

        $projectPriority = ProjectPriorities::findOrFail($id);
        $projectPriority->update($request->all());
        return redirect()->route('projects.show', $projectPriority->id_project)
            ->with('success', 'Priorita byla úspěšně upravena.');
    }
    public function destroy($id){
        $projectPriority = ProjectPriorities::findOrFail($id);
        $projectPriority->delete();
        return redirect()->route('projects.show', $projectPriority->id_project)
            ->with('success', 'Priorita byla úspěšně smazána.');
    }
    public function show($id){
        $projectPriority = ProjectPriorities::findOrFail($id);
        return view('project_priorities.show', compact('projectPriority'));
    }
    public function getProjectPrioritiesByProjectId($id){
        $projectPriorities = ProjectPriorities::where('id_project', $id)->get();
        return response()->json($projectPriorities);
    }

}
