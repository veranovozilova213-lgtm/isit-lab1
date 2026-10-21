<?php
// Эта строчка обязательно должна быть самой первой!
declare(strict_types=1);

// А теперь уже включаем показ ошибок
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

require APP_PATH . '/autoload.php';
require APP_PATH . '/helpers.php';

$router = new App\Core\Router();
require BASE_PATH . '/routes/web.php';

try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1 style="color:red;">ОШИБКА:</h1>';
    echo '<p>' . $e->getMessage() . '</p>';
    echo '<p>Файл: ' . $e->getFile() . ' (строка ' . $e->getLine() . ')</p>';
}