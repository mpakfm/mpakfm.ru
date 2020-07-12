#!/usr/bin/env php
<?php

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;

if (!in_array(PHP_SAPI, ['cli', 'phpdbg', 'embed'], true)) {
    echo 'Warning: The console should be invoked via the CLI version of PHP, not the ' . PHP_SAPI . ' SAPI' . PHP_EOL;
}

set_time_limit(0);

include_once dirname(__DIR__) . '/../vendor/autoload.php';

if (!class_exists(Application::class) || !class_exists(Dotenv::class)) {
    throw new LogicException('You need to add "symfony/framework-bundle" and "symfony/dotenv" as Composer dependencies.');
}

(new Dotenv())->bootEnv(dirname(__DIR__) . '/../.env.test');

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    if (class_exists(Debug::class)) {
        Debug::enable();
    }
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

echo $_SERVER['APP_ENV'];
