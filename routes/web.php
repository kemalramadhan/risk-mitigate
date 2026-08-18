<?php

use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpertWeightingController;
use App\Http\Controllers\FormulationJudgmentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pencarian proyek (nama / unique ID)
    Route::get('/projects/search', [ProjectController::class, 'search'])->name('projects.search');

    // Pendaftaran & detail proyek
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

    // Anggota proyek + enable mitigasi (owner)
    Route::post('/projects/{project}/members', [ProjectMemberController::class, 'store'])->name('members.store');
    Route::delete('/projects/{project}/members/{member}', [ProjectMemberController::class, 'destroy'])->name('members.destroy');
    Route::get('/projects/{project}/members/lookup', [ProjectMemberController::class, 'lookup'])->name('members.lookup');
    Route::patch('/projects/{project}/enable', [ProjectMemberController::class, 'toggleEnable'])->name('projects.enable');

    // Pembobotan Expert (owner)
    Route::get('/projects/{project}/weighting', [ExpertWeightingController::class, 'edit'])->name('weighting.edit');
    Route::post('/projects/{project}/weighting', [ExpertWeightingController::class, 'update'])->name('weighting.update');
    Route::delete('/projects/{project}/weighting', [ExpertWeightingController::class, 'reset'])->name('weighting.reset');

    // Assessment (perumusan strategi untuk 4 level sekaligus)
    Route::get('/projects/{project}/assessments/create', [AssessmentController::class, 'create'])->name('assessments.create');
    Route::post('/projects/{project}/assessments', [AssessmentController::class, 'store'])->name('assessments.store');
    Route::get('/assessments/{assessment}', [AssessmentController::class, 'show'])->name('assessments.show');
    Route::post('/assessments/{assessment}/compute', [AssessmentController::class, 'compute'])->name('assessments.compute');

    // Form penilaian pairwise per level (AHP standar / AHP-Express)
    Route::get('/formulations/{formulation}/judge', [FormulationJudgmentController::class, 'edit'])->name('judgments.edit');
    Route::post('/formulations/{formulation}/judge', [FormulationJudgmentController::class, 'update'])->name('judgments.update');
});
