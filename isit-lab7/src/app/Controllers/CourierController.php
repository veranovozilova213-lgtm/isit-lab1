<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\CourierRepository;

final class CourierController
{
    private CourierRepository $couriers;

    public function __construct()
    {
        $this->couriers = new CourierRepository();
    }

    public function index(): void
    {
        $couriers = $this->couriers->all();
        view('projects/index', ['title' => 'Курьеры', 'projects' => $couriers]);
    }

    public function show(string $id): void
    {
        $courier = $this->couriers->find((int) $id);
        if ($courier === null) {
            notFound();
        }
        view('projects/show', ['title' => $courier['title'], 'project' => $courier]);
    }
}