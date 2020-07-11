<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 11.07.2020
 * Time: 2:28.
 */

namespace App\Service\PhpCsFixer;

use App\Service\ConsoleColors;
use App\Service\ProcessExecutor\ProcessExecutor;
use LogicException;
use PhpCsFixer\ConfigInterface;
use RuntimeException;

class PhpCsFixerFilesAnalyzer
{
    /** @var ConfigInterface */
    private $config;

    /** @var string */
    private $configPath;

    public function __construct($configPath)
    {
        $this->configPath = $configPath;
        $this->config     = include $configPath;
    }

    /**
     * @param string   $filePath
     * @param string   $ruleName
     * @param null|int $limit
     *
     * @return array of DiffLine[]
     */
    public function getFileDiffForRule($filePath, $ruleName, $limit = null)
    {
        $rules = $this->config->getRules();
        if (!array_key_exists($ruleName, $rules)) {
            throw new LogicException("Rule '{$ruleName}' not found");
        }

        $phpCsFixerExecutable = __DIR__ . '/../../../vendor/bin/php-cs-fixer';
        $phpCsFixerConfigPath = $this->configPath;

        $processExecutor = new ProcessExecutor();

        $str = sprintf(
            '%s fix --config=%s --cache-file=%s --dry-run --diff --diff-format=udiff -- %s',
            escapeshellarg($phpCsFixerExecutable),
            escapeshellarg($phpCsFixerConfigPath),
            escapeshellarg('.php_cs.cache'),
            escapeshellarg($filePath)
        );

        $result = $processExecutor->exec($str);

        if (0 === $result->getExitCode()) {
            return [];
        }

        if (8 !== $result->getExitCode()) {
            var_dump($result->getErrorOutput());

            throw new RuntimeException('Unexpected exit code: ' . $result->getExitCode());
        }

        $outputLines = $result->getStandardOutputLines();

        // Удаляем лишние строки из результата
        // Явно проверяем, что удаляем, чтобы не удалить что-то не то
        if (
            count($outputLines) >= 4
            && (false !== mb_strpos($outputLines[1], '---------- begin diff ----------'))
            && (false !== mb_strpos($outputLines[2], '--- Original'))
            && (false !== mb_strpos($outputLines[3], '+++ New'))
        ) {
            unset($outputLines[0], $outputLines[1], $outputLines[2], $outputLines[3]); // 1) /path/to/file.php (rule_name)
             // ---------- begin diff ----------
             // --- Original
             // +++ New
        }

        $oldLineNumber  = 0;
        $newLineNumber  = 0;
        $diffLineGroups = [];
        $diffLines      = [];
        foreach ($outputLines as $line) {
            if (false !== mb_strpos($line, 'No newline at end of file')) {
                continue;
            }

            if (preg_match('/@@ -(\d+)(?:,\d+)?\s*\+(\d+)(?:,\d+)?/', $line, $matches)) {
                $oldLineNumber = ($matches[1] - 1);
                $newLineNumber = ($matches[2] - 1);
                if ($diffLines) {
                    $diffLineGroups[] = $diffLines;
                    $diffLines        = [];

                    if ($limit && count($diffLineGroups) == $limit) {
                        break;
                    }
                }

                continue;
            }

            // Всё, что идет после «end diff» не обрабатываем
            if (false !== mb_strpos($line, '----------- end diff -----------')) {
                break;
            }

            $diffLine = new DiffLine($line);

            if (!$diffLine->isInsert()) {
                ++$oldLineNumber;
            }

            if (!$diffLine->isDelete()) {
                ++$newLineNumber;
            }

            $diffLine->oldLineNumber = (!$diffLine->isInsert()) ? $oldLineNumber : null;
            $diffLine->newLineNumber = (!$diffLine->isDelete()) ? $newLineNumber : null;

            $diffLines[] = $diffLine;
        }

        if ($diffLines) {
            $diffLineGroups[] = $diffLines;
        }

        return $diffLineGroups;
    }

    /**
     * @return string
     */
    public static function convertDiffToString(array $diffLineGroups)
    {
        $result = '';

        if (empty($diffLineGroups)) {
            return $result;
        }

        $oldMaxLineNumberLength = 0;
        $newMaxLineNumberLength = 0;

        /** @var DiffLine[] $lastDiffGroup */
        $lastDiffGroup = end($diffLineGroups);
        if ($lastDiffGroup) {
            foreach ($lastDiffGroup as $diffLine) {
                $oldMaxLineNumberLength = max($oldMaxLineNumberLength, strlen((string) $diffLine->oldLineNumber));
                $newMaxLineNumberLength = max($newMaxLineNumberLength, strlen((string) $diffLine->newLineNumber));
            }
        }

        /** @var DiffLine[] $difflines */
        foreach ($diffLineGroups as $difflines) {
            $result .= "\n";
            foreach ($difflines as $diffLine) {
                if ($diffLine->isDelete()) {
                    $coloredText = ConsoleColors::red($diffLine->text);
                } elseif ($diffLine->isInsert()) {
                    $coloredText = ConsoleColors::green($diffLine->text);
                } else {
                    $coloredText = $diffLine->text;
                }

                $result .= str_pad((string) $diffLine->oldLineNumber, $oldMaxLineNumberLength + 1, ' ', STR_PAD_LEFT);
                $result .= str_pad((string) $diffLine->newLineNumber, $newMaxLineNumberLength + 1, ' ', STR_PAD_LEFT);
                $result .= ' ' . $coloredText . "\n";
            }
        }

        return $result;
    }
}
