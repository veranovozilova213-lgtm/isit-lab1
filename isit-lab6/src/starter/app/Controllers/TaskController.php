<?php
// app/Controllers/TaskController.php — контроллер задач: список, просмотр, создание.

declare(strict_types=1);

namespace App\Controllers;

use App\Models\TaskRepository;

final class TaskController
{
    private TaskRepository $tasks;

    public function __construct()
    {
        $this->tasks = new TaskRepository();
    }

    // GET /tasks
    public function index(): void
    {
        $status = $_GET['status'] ?? '';
        $tasks = $this->tasks->all();
        
        if ($status !== '') {
            $tasks = array_values(array_filter($tasks, fn(array $t): bool => $t['status'] === $status));
        }
        
        view('tasks/index', ['title' => 'Список заказов', 'tasks' => $tasks, 'status' => $status]);
    }

    // GET /tasks/5
    public function show(string $id): void
    {
        $task = $this->tasks->find((int) $id);
        
        if ($task === null) {
            notFound();
        }
        
        view('tasks/show', [
            'title' => $task['title'],
            'task'  => $task
        ]);
    }

    // GET /tasks/create
    public function create(): void
    {
        view('tasks/create', [
            'title'    => 'Новый заказ',
            'errors'   => [],
            'old'      => [],
        ]);
    }

    // POST /tasks
    public function store(): void
    {
        $title = trim($_POST['title'] ?? '');
        $courier = trim($_POST['courier'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $sum = (int) ($_POST['sum'] ?? 0);

        $errors = [];
        if (mb_strlen($title) < 2) {
            $errors['title'] = 'Введите номер заказа (минимум 2 символа)';
        }
        if ($courier === '') {
            $errors['courier'] = 'Укажите курьера';
        }
        if ($address === '') {
            $errors['address'] = 'Укажите адрес доставки';
        }

        if ($errors) {
            http_response_code(422);
            view('tasks/create', [
                'title'    => 'Новый заказ',
                'errors'   => $errors,
                'old'      => $_POST,
            ]);
            return;
        }

        $this->tasks->add([
            'title'   => $title,
            'status'  => 'new',
            'courier' => $courier,
            'address' => $address,
            'sum'     => $sum,
        ]);

        redirect('/tasks');
    }
}