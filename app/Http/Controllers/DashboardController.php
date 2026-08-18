<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $memberProjectIds = $user->memberships()->pluck('project_id');

        $projects = Project::query()
            ->with('owner')
            ->withCount(['members', 'assessments'])
            ->whereIn('id', $memberProjectIds)
            ->orWhere('owner_id', $user->id)
            ->latest()
            ->get()
            ->unique('id');

        $stats = [
            'total'  => $projects->count(),
            'owned'  => $projects->where('owner_id', $user->id)->count(),
            'shared' => $projects->where('owner_id', '!=', $user->id)->count(),
        ];

        return view('dashboard.index', compact('projects', 'stats'));
    }
}
