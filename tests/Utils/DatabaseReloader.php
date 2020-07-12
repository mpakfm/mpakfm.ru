<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 11.07.2020
 * Time: 19:57.
 */

namespace App\Tests\Utils;

use App\Tests\TestCaseAbstract;
use App\Tools\Paths;
use Doctrine\ORM\EntityManager;
use Exception;

class DatabaseReloader
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $testDbName;

    /**
     * @var string
     */
    private $prodDbName;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;

        $this->testDbName = $em->getConnection()->getDatabase();
        $this->prodDbName = str_replace('_test', '', $this->testDbName);
        if ($this->prodDbName == $this->testDbName) {
            TestCaseAbstract::markTestSkipped("База данных для тестов должна отличаться от продакшена!\nТестовая база данных полностью очищается при каждом запуске тестов.");
        }
    }

    private function __clone()
    {
    }

    /**
     * Очищает тестовую базу данных до состояния из bootstrap.sql.
     *
     * @param bool $saveCleanState если false, то просто чистим базу без restore
     */
    public function cleanDatabase(TestCaseAbstract $test)
    {
        $dumpFile = $this->createDumpFile($test);
        $command  = 'mysql < ' . escapeshellarg($dumpFile);
        // Создаём тестовую базу из дампа структуры продакшена
        $this->exec($command);
    }

    /**
     * Делаем дамп для создания тестовой базы.
     */
    private function createDumpFile(TestCaseAbstract $test): string
    {
        $dumpFile = tempnam(Paths::getTestTmpPath(), 'autotests_dump_'); // Файл в tmp-директории
        // мы чистим Paths::getTestTmpPath() перед запуском тестов, руками эти файлы удалять не надо

        // в начало файла с дампом пишем команды для пересоздания тестовой базы
        $sql =
            "DROP DATABASE IF EXISTS `{$this->testDbName}`;\n"
            . "CREATE DATABASE `{$this->testDbName}` DEFAULT CHARACTER SET = utf8;\n"
            . "USE `{$this->testDbName}`;\n";
        $command = 'echo ' . escapeshellarg($sql) . ' > ' . escapeshellarg($dumpFile);
        $this->exec($command);

        // Делаем дамп базы продакшена без данных, только структура таблиц, триггеры и т.п.
        $command = 'mysqldump --no-create-db --no-data --routines ' . escapeshellarg($this->prodDbName) . ' >> ' . escapeshellarg($dumpFile);
        $this->exec($command);

        // Добавляем в дамп файл bootstrap.sql, в котором лежат команды для наполнения базы минимально необходимыми данными
        $command = 'cat ' . escapeshellarg(Paths::getTestBootstrapFile()) . ' >> ' . escapeshellarg($dumpFile);
        $this->exec($command);

        return $dumpFile;
    }

    private function exec(string $cmd)
    {
        exec($cmd, $out, $err);
        if ($err) {
            throw new Exception("Command execution failed: {$cmd}");
        }
    }
}
