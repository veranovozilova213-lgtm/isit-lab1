<?php
declare(strict_types=1);
namespace App\Controllers\Api;

use App\Models\CourierRepository;
use App\Models\OrderRepository;

final class CourierApiController
{
    private CourierRepository $couriers;

    public function __construct()
    {
        $this->couriers = new CourierRepository();
    }

    public function index(): void
    {
        json_response($this->couriers->all());
    }

    public function show(string $id): void
    {
        $courier = $this->couriers->find((int) $id);
        if ($courier === null) {
            json_error('Курьер не найден', 404);
        }
        json_response($courier);
    }

    public function orders(string $id): void
    {
        $courier = $this->couriers->find((int) $id);
        if ($courier === null) {
            json_error('Курьер не найден', 404);
        }
        
        $orderRepo = new OrderRepository();
        $allOrders = $orderRepo->all();
        $courierOrders = array_values(array_filter($allOrders, fn(array $o): bool => $o['courier_id'] === (int) $id));
        
        json_response($courierOrders);
    }

    public function store(): void
    {
        $data = json_input();
        $errors = $this->validate($data);
        if ($errors) {
            json_error('Ошибка проверки данных', 422, $errors);
        }
        $courier = $this->couriers->add($this->clean($data));
        header('Location: /api/couriers/' . $courier['id']);
        json_response($courier, 201);
    }

    public function update(string $id): void
    {
        $courier = $this->couriers->find((int) $id);
        if ($courier === null) {
            json_error('Курьер не найден', 404);
        }
        $data = array_merge($courier, json_input());
        $errors = $this->validate($data);
        if ($errors) {
            json_error('Ошибка проверки данных', 422, $errors);
        }
        json_response($this->couriers->update($courier['id'], $this->clean($data)));
    }

    public function destroy(string $id): void
    {
        if (!$this->couriers->delete((int) $id)) {
            json_error('Курьер не найден', 404);
        }
        no_content();
    }

    private function clean(array $data): array
    {
        return [
            'title'       => trim($data['title'] ?? ''),
            'description' => trim($data['description'] ?? ''),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty($data['title']) || mb_strlen(trim($data['title'])) < 3) {
            $errors['title'] = 'Имя курьера — не короче 3 символов';
        }
        return $errors;
    }
}