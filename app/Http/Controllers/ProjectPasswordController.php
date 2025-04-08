<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\ProjectPassword;
use App\Http\Middleware\IsAdmin;
use Illuminate\Routing\Controller;

class ProjectPasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $projectPasswords = ProjectPassword::with('project.customer')->paginate(10);
        return view('projects.index', compact('projectPasswords'));
    }

    public function create(Request $request)
    {
        $selectedProject = null;

        if ($request->has('id_project')) {
            $selectedProject = Project::with('customer')->findOrFail($request->id_project);
        }
        return view('project_passwords.create', compact('selectedProject'));
    }
    public function store(Request $request)
    {

        $validated = $request->validate([
            'id_project' => 'required|exists:projects,id',
            'name' => 'required|string|max:100',
            'login' => 'required|string|max:100',
            'password' => 'required|string|max:100',
        ]);

        ProjectPassword::create($validated);

        return redirect()->route('projects.show', $validated['id_project'])
            ->with('success', 'Heslo bylo úspěšně vytvořeno.');
    }
    public function edit(ProjectPassword $projectPassword)
    {
        return view('project_passwords.edit', compact('projectPassword'));
    }
    public function update(Request $request, ProjectPassword $projectPassword)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'login' => 'required|string|max:100',
            'password' => 'required|string|max:100',
        ]);

        $projectPassword->update($validated);

        return redirect()->route('projects.show', $projectPassword->id_project)
            ->with('success', 'Heslo bylo úspěšně aktualizováno.');
    }
    public function destroy(ProjectPassword $projectPassword)
    {
        $projectPassword->delete();

        return redirect()->route('projects.show', $projectPassword->id_project)
            ->with('success', 'Heslo bylo úspěšně smazáno.');
    }
    public function show(ProjectPassword $projectPassword)
    {
        return view('project_passwords.show', compact('projectPassword'));
    }
}
