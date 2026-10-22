<?php
declare(strict_types=1);
namespace App\Controllers\Api;

use App\Models\CustomerRepository;

final class CustomerApiController
{
    private CustomerRepository $customers;

    public function __construct()
    {
        $this->customers = new CustomerRepository();
    }

    public function index(): void
    {
        json_response($this->customers->all());
    }

    public function show(string $id): void
    {
        $customer = $this->customers->find((int) $id);
        if ($customer === null) {
            json_error('Клиент не найден', 404);
        }
        json_response($customer);
    }

    public function store(): void
    {
        $data = json_input();
        $errors = $this->validate($data);
        if ($errors) {
            json_error('Ошибка проверки данных', 422, $errors);
        }
        $customer = $this->customers->add($this->clean($data));
        header('Location: /api/customers/' . $customer['id']);
        json_response($customer, 201);
    }

    public function update(string $id): void
    {
        $customer = $this->customers->find((int) $id);
        if ($customer === null) {
            json_error('Клиент не найден', 404);
        }
        $data = array_merge($customer, json_input());
        $errors = $this->validate($data);
        if ($errors) {
            json_error('Ошибка проверки данных', 422, $errors);
        }
        json_response($this->customers->update($customer['id'], $this->clean($data)));
    }

    public function destroy(string $id): void
    {
        if (!$this->customers->delete((int) $id)) {
            json_error('Клиент не найден', 404);
        }
        no_content();
    }

    private function clean(array $data): array
    {
        return [
            'name'  => trim($data['name'] ?? ''),
            'email' => trim($data['email'] ?? ''),
            'phone' => trim($data['phone'] ?? ''),
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty($data['name']) || mb_strlen(trim($data['name'])) < 3) {
            $errors['name'] = 'Имя клиента — не короче 3 символов';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Укажите корректный email';
        }
        return $errors;
    }
}