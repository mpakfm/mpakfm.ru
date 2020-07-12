<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 11.07.2020
 * Time: 16:14.
 */

namespace App\Tests;

use App\Service\ProcessExecutor\ProcessExecutor;
use App\Tools\Paths;

/**
 * @internal
 * @coversNothing
 */
class ProcessExecutorTest extends TestCaseAbstract
{
    /**
     * Проверяет, чтобы при запуске консольного скрипта из теста правильно определялось testing окружение.
     */
    public function testEnvironment()
    {
        $scriptPath = Paths::getTestsPath() . '/console/show-environment.php';

        $executor = new ProcessExecutor();
        $result   = $executor->exec('php ' . escapeshellarg($scriptPath));

        assertSame(0, $result->getExitCode());
        assertSame('test', $result->getStandardOutput());
        assertEmpty($result->getErrorOutput(), $result->getErrorOutput());
    }
}
