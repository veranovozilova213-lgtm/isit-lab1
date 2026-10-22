# Документация REST API «Приложение доставки»

## Сводная таблица

| Метод | URL | Назначение | Успешный код |
|---|---|---|---|
| GET | `/api/orders` | Список заказов (фильтр `?status=`) | 200 |
| GET | `/api/orders/{id}` | Заказ по номеру | 200 |
| POST | `/api/orders` | Создать заказ | 201 |
| PATCH | `/api/orders/{id}` | Изменить заказ | 200 |
| DELETE | `/api/orders/{id}` | Удалить заказ | 204 |
| GET | `/api/couriers` | Список курьеров | 200 |
| GET | `/api/couriers/{id}` | Курьер по номеру | 200 |
| GET | `/api/couriers/{id}/orders` | Заказы курьера | 200 |
| GET | `/api/customers` | Список клиентов | 200 |

## Примеры запросов

### POST /api/orders — создать заказ

**Тело запроса (JSON):**
```json
{
  "title": "Заказ № 999",
  "courier_id": 1,
  "address": "г. Москва, ул. Пушкина, 1",
  "sum": 1500
}