<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 11.07.2020
 * Time: 2:08.
 */

namespace App\Tests\PhpCsFixer;

use App\Service\PhpCsFixer\PhpCsFixerFilesAnalyzer;
use App\Service\PhpCsFixer\RuleSetDefinition;
use App\Service\ProcessExecutor\ExecutionResult;
use App\Service\ProcessExecutor\ProcessExecutor;
use App\Tests\TestCaseAbstract;
use PhpCsFixer\Console\Command\FixCommandExitStatusCalculator;

/**
 * @internal
 * @coversNothing
 */
class PhpCsFixerTest extends TestCaseAbstract
{
    /**
     * @var ProcessExecutor
     */
    private $process;

    /**
     * Проверяет code style в php файлах.
     */
    public function testPhpFilesCodeStyle()
    {
        $this->assertNoCodeStyleProblems('.php_cs.dist');
    }

    /**
     * @param string $configFilename
     */
    private function assertNoCodeStyleProblems($configFilename)
    {
        $this->process = new ProcessExecutor();

        $changedFiles = $this->getChangedFiles();

        // Если измененных файлов нет, то проверяем весь проект
        if (!$changedFiles) {
            $changedFiles = ['.'];
        }

        $executionResult = $this->process->exec(sprintf(
            'cd %s; vendor/bin/php-cs-fixer fix --config=%s --dry-run --verbose --path-mode=intersection -- %s',
            escapeshellarg(__DIR__ . '/../../'),
            escapeshellarg($configFilename),
            //$configFilename,
            implode(' ', array_map('escapeshellarg', $changedFiles))
        ));

        $message = $this->getPhpCsFixerCommandResultMessage($executionResult, $configFilename);
        $this->assertSame(0, $executionResult->getExitCode(), $message);
    }

    /**
     * @return string[]
     */
    private function getChangedFiles()
    {
        // Поиск ближайшего общего предка между текущим коммитом и origin/master
        $executionResult = $this->process->exec(
            'git merge-base origin/master HEAD'
        );
        $this->assertSame(0, $executionResult->getExitCode(), $executionResult->getErrorOutput());
        $mergeBase = $executionResult->getStandardOutputLines()[0] ?? '';
        $this->assertNotEmpty($mergeBase, 'Merge base between origin/master and HEAD not found');

        // Получим список измененных файлов (в сделанных коммитах и рабочей директории)
        $executionResult = $this->process->exec(sprintf(
            'git diff --name-only --diff-filter=ACMRTUXB %s',
            escapeshellarg($mergeBase)
        ));
        $this->assertSame(0, $executionResult->getExitCode(), $executionResult->getErrorOutput());

        // Исключим пустые строки, обычно это последняя строка, из-за переноса
        return array_filter($executionResult->getStandardOutputLines(), function ($line) {
            return '' != $line;
        });
    }

    /**
     * @param string $configFilename
     *
     * @return string
     */
    private function getPhpCsFixerCommandResultMessage(ExecutionResult $consoleCommand, $configFilename)
    {
        $exitCode = $consoleCommand->getExitCode();
        $stdErr   = $consoleCommand->getErrorOutput();
        $messages = [];

        // @see https://github.com/FriendsOfPHP/PHP-CS-Fixer#exit-codes
        if ($exitCode === 1) {
            return "General error (or PHP minimal requirement not matched).\n{$stdErr}\n";
        }

        if ($exitCode & FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_FILES) {
            $messages[] = $stdErr;
        }

        if ($exitCode & FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_CHANGED_FILES) {
            $analyzer = new PhpCsFixerFilesAnalyzer(__DIR__ . '/../../' . $configFilename);

            $messages[] = 'Some files need fixing.';
            $messages[] = $this->reformatPhpCsFixerStdOut($analyzer, $consoleCommand->getStandardOutputLines());
            $messages[] = 'Run command below to automatically fix them:';
            $messages[] = "vendor/bin/php-cs-fixer fix --config={$configFilename}";
        }

        if ($exitCode & FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_CONFIG) {
            return "Configuration error of the application.\n{$stdErr}\n";
        }

        if ($exitCode & FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_HAS_INVALID_FIXER_CONFIG) {
            return "Configuration error of a Fixer.\n{$stdErr}\n";
        }

        if ($exitCode & FixCommandExitStatusCalculator::EXIT_STATUS_FLAG_EXCEPTION_IN_APP) {
            return "Exception raised within the application.\n{$stdErr}\n";
        }

        if (255 === $exitCode) {
            return "PHP error.\n{$stdErr}\n";
        }

        return implode("\n", $messages) . "\n";
    }

    /**
     * @param string[] $standardOutputLines
     *
     * @return string
     */
    private function reformatPhpCsFixerStdOut(PhpCsFixerFilesAnalyzer $analyzer, array $standardOutputLines)
    {
        $result = '';

        // Переформатируем строки с файлами.
        //
        // Было:
        // 1) /path/to/file.php (indentation_type, array_syntax)
        //
        // Стало:
        // 1) Code must use configured indentation type (4 spaces):
        //  /path/to/file.php:74
        // 2) PHP arrays should be declared using short syntax:
        //  /path/to/file.php:95

        $i = 1;
        foreach ($standardOutputLines as $line) {
            if (preg_match('/^\s*\d+\)\s(.+)\s\((.+)\)/', $line, $matches)) {
                $filePath  = $matches[1];
                $ruleNames = explode(',', $matches[2]);
                $ruleNames = array_map('trim', $ruleNames);

                foreach ($ruleNames as $ruleName) {
                    $errorMessage = RuleSetDefinition::getRuleDescription($ruleName);

                    // Чтобы в консоли PhpStorm ссылка на файл была кликабельной,
                    // после пути к файлу не должно идти других символов, а впереди нужен один пробел.
                    $result .= "{$i}) {$errorMessage}:\n {$filePath}";

                    // Всего показываем не больше 10 ошибок на каждое правило в каждом файле
                    $diffGroups = $analyzer->getFileDiffForRule($filePath, $ruleName, 10);

                    // Находим строку с первой ошибкой
                    if ($diffGroups) {
                        /** @var DiffLine $diffLine */
                        foreach ($diffGroups[0] as $diffLine) {
                            if ($diffLine->isModified() && $diffLine->oldLineNumber) {
                                $result .= ":{$diffLine->oldLineNumber}";

                                break;
                            }
                        }
                    }

                    $result .= "\n";
                    $result .= PhpCsFixerFilesAnalyzer::convertDiffToString($diffGroups);

                    $i++;
                }
            } else {
                $result .= "{$line}\n";
            }
        }

        return $result;
    }
}
