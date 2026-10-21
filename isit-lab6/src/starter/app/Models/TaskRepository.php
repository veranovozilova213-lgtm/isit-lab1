<?php
// app/Models/TaskRepository.php — модель: единственное место, которое знает, где и как хранятся задачи.
// Сейчас задачи лежат в файле storage/tasks.json. В следующих работах файл заменит база данных,
// а контроллеры и шаблоны при этом менять не придётся: они работают только с методами этого класса.

declare(strict_types=1);

namespace App\Models;

final class TaskRepository
{
    private string $file;

    public function __construct()
    {
        $this->file = BASE_PATH . '/storage/tasks.json';
    }

    // Все задачи (массив ассоциативных массивов)
    public function all(): array
    {
        return json_decode(file_get_contents($this->file), true) ?? [];
    }

    // Задача по номеру или null, если такой нет
    public function find(int $id): ?array
    {
        foreach ($this->all() as $task) {
            if ($task['id'] === $id) {
                return $task;
            }
        }
        return null;
    }

    // Добавляет задачу, присваивает ей новый номер и сохраняет файл. Возвращает созданную задачу.
    public function add(array $data): array
    {
        $tasks = $this->all();
        $ids = array_column($tasks, 'id');
        // Берем максимальный ID и прибавляем 1. Если массив пуст, начинаем с 1.
        $task = ['id' => ($ids ? max($ids) : 0) + 1] + $data;
        $tasks[] = $task;

        file_put_contents(
            $this->file,
            json_encode($tasks, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            LOCK_EX
        );
        return $task;
    }
}