<?php
// app/Controllers/ProjectController.php — контроллер курьеров.

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ProjectRepository;

final class ProjectController
{
    // GET /projects
    public function index(): void
    {
        $projects = (new ProjectRepository())->all();
        view('projects/index', [
            'title' => 'Курьеры',
            'projects' => $projects
        ]);
    }

    // GET /projects/2
    public function show(string $id): void
    {
        $project = (new ProjectRepository())->find((int) $id);
        
        if ($project === null) {
            notFound();
        }
        
        // Передаем только данные курьера (без вызова задач)
        view('projects/show', [
            'title'   => $project['title'],
            'project' => $project
        ]);
    }
}