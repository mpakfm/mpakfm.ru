<?php

namespace App\Tools;

class Paths
{
    private static $localPath;

    public static function getConfigsPath(): string
    {
        return static::getRootPath() . '/config';
    }

    public static function getTestsPath(): string
    {
        return static::getRootPath() . '/tests';
    }

    public static function getTestBootstrapFile(): string
    {
        return static::getRootPath() . '/tests/bootstrap.sql';
    }

    public static function getTestTmpPath(): string
    {
        return self::createIfNotExists(static::getRootPath() . '/tmp/autotests');
    }

    public static function getRootPath(): string
    {
        $namespaceParts = explode('\\', __NAMESPACE__);

        return dirname(__DIR__, count($namespaceParts));
    }

    private static function createIfNotExists($path): string
    {
        if (!file_exists($path)) {
            // race condition тут происходит редко, но стабильно
            // выхода нет, придётся засобачить
            $isCreated = @mkdir($path, 0777, true);
            if (!$isCreated && !file_exists($path)) {
                $error = error_get_last();
                trigger_error($error['message'], E_USER_WARNING);
            }
        }

        return $path;
    }
}
