<?php

use App\Tools\Paths;
use Mpakfm\Printu;
use PHPUnit\Framework\Assert;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

Printu::setPath(__DIR__ . '/../var/log');

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}
putenv('APP_ENV=test');

$path = Paths::getTestTmpPath();
if (file_exists($path)) {
    foreach (glob($path . '/*') as $file) {
        unlink($file);
    }
}

// Загружаем assert-функции из phpunit
require_once dirname((new ReflectionClass(Assert::class))->getFileName()) . '/Assert/Functions.php';
