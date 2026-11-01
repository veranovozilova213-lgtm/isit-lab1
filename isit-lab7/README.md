# **Лабораторная работа №7**



### **Проектирование REST API**





##### **Цель работы**

Изучить принципы проектирования REST API, разработать набор ресурсов информационной системы, реализовать основные HTTP-методы, подготовить документацию API и выполнить тестирование запросов.



##### **Краткое описание выполненного задания**



На основе серверной части из лабораторной работы №6 (предметная область «Приложение доставки заказов», PHP 8.2, MVC) реализован REST API с тремя ресурсами: заказы, курьеры, клиенты. Для каждого ресурса поддержаны операции CRUD через методы `GET`, `POST`, `PUT / PATCH`, `DELETE`; ответы возвращаются в формате JSON с корректными кодами состояния (200, 201, 204, 400, 404, 405, 409, 422, 500). Сайт из лабораторной работы №6 продолжает работать с новым дизайном.







##### **Предметная область**



|**Что определить**|**Значение**|
|-|-|
|Основная сущность|заказ (`order`, `orders`)|
|Связанная сущность|курьер (`courier`, `couriers`)|
|Третья сущность (вместо участников)|клиент (`customer`, `customers`)|
| Поля основной сущности|`id`, `courier\\\_id`, `title`, `status` (`new`, `in\\\_progress`, `done`), `address`, `sum`|
|Поля третьей сущности|`id`, `name`, `email`, `phone`|



**Запуск**



cd isit-lab1/isit-lab7/src

php -S localhost:8000 -t public







##### **Описание реализованных ресурсов**



|**Ресурс**|**URL**|**Методы**|**Назначение**|**Формат данных**|
|-|-|-|-|-|
|Заказы|`/api/orders`, `/api/orders/{id}`|GET, POST, PUT/PATCH, DELETE|учёт заказов на доставку|`id`, `courier\\\_id`, `title`, `status`, `address`, `sum`|
|Куьеры|`/api/couriers`, `/api/couriers/{id}`, `/api/couriers/{id}/orders`|GET, POST, PUT/PATCH, DELETE|курьеры и их назначенные заказы|`id`, `title`, `description`|
|Клиенты|`/api/customers`, `/api/customers/{id}`|GET, POST, PUT/PATCH, DELETE|база клиентов (получателей)|`id`, `name`, `email`, `phone`|







##### **Описание REST API**



|**Метод**|**URL**|**Назначение**|**Успешный код**|
|-|-|-|-|
|GET|`/api/orders`|список заказов (фильтр `status`)|200|
|GET|`/api/orders/{id}`|заказ по номеру|200|
|POST|`/api/orders`|создать заказ|201|
|PUT, PATCH|`/api/orders/{id}`|изменить заказ|200|
|DELETE|`/api/orders/{id}`|удалить заказ|204|
|CET, POST|`/api/couriers`|список / создание курьера|200/201|
|GET, PUT, PATCH, DELETE|`/api/couriers/{id}`|курьер: чтение, изменение, удаление|200/204|
|GET|`/api/couriers/{id}/orders`|заказы курьера|200|
|GET|`/api/customers`|список / создание клиента|200/201|
|GET|`/api/customers/{id}`|клиент: чтение, изменение, удаление|200/204|







###### **Устройство реализации**



маршруты routes/web.php; маршрутизатор Router поддерживает get, post, put, patch, delete и сам возвращает JSON-ответы 404 и 405 (с заголовком Allow) для адресов /api/...

контроллеры app/Controllers/Api/: по пять методов index, show, store, update, destroy; PUT и PATCH обрабатываются одним методом update (присланные поля заменяют старые)

модели наследуют от JsonRepository (all, find, add, update, delete), данные в storage/\*.json

помощники json\_response(), json\_error(), no\_content(), json\_input() в app/helpers.php

данные проверяются на сервере (validate()), ошибки возвращаются все сразу в формате {"error": "...", "errors": {"поле": "..."}}







#### **Примеры запросов и ответов**

<img width="1455" height="789" alt="01" src="https://github.com/user-attachments/assets/5b26622a-98ab-4510-bed0-91ab26ed68b6" />


#### 

###### Создание задачи

<img width="863" height="258" alt="07" src="https://github.com/user-attachments/assets/0f569cd2-4bac-47e8-a72c-461b4934fa67" />



###### Ошибка проверки







#### **Тестирование**



###### GET: список с фильтром и задачи проекта


<img width="1455" height="789" alt="01" src="https://github.com/user-attachments/assets/2a68db36-f4eb-4d51-b15a-479149b145e0" />


###### POST: создание (201, Location)

<img width="860" height="309" alt="09" src="https://github.com/user-attachments/assets/b77b3796-5491-451f-944e-19532e0babae" />


###### PATCH: изменение

<img width="863" height="258" alt="08" src="https://github.com/user-attachments/assets/fdb21efc-f2ee-452d-b397-471213257e3c" />



###### DELETE: 204 и повторное удаление (404)

<img width="861" height="106" alt="11" src="https://github.com/user-attachments/assets/83d98a97-4106-49d9-abfb-db2fd9d8bc38" />



###### Ошибки 422 и 400


<img width="861" height="252" alt="10" src="https://github.com/user-attachments/assets/beb42248-9227-4250-888b-d260dc675a40" />


###### Ошибка "заказ не найден"


<img width="686" height="209" alt="03" src="https://github.com/user-attachments/assets/374388bc-5ffb-43f1-90e4-ffb4d3c31aa3" />





##### **Вывод**



В результате работы спроектированы три ресурса информационной системы: заказы, курьеры, клиенты. Реализован REST API на PHP: адреса строятся по правилам REST, действия задаются HTTP-методами, поддерживаются все операции CRUD, данные передаются в JSON, а результат сообщается кодами состояния 200, 201, 204, 400, 404, 405, 409 и 422. Серверная проверка входных данных и единый формат ошибок облегчают работу клиентского приложения, а документация (`docs/api.md`) и таблица тестирования (`docs/testing.md`) позволяют воспроизвести и проверить каждый запрос. 





