<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 11.07.2020
 * Time: 2:16.
 */

namespace App\Service\ProcessExecutor;

class ExecutionResult extends ExitCodeResult
{
    /** @var string */
    protected $standardOutput;
    /** @var string */
    protected $errorOutput;

    public function getStandardOutput(): string
    {
        return $this->standardOutput ?? '';
    }

    public function getErrorOutput(): string
    {
        return $this->errorOutput ?? '';
    }

    /**
     * @return string[] Без \n на концах строк
     */
    public function getStandardOutputLines(): array
    {
        if (empty($this->standardOutput)) {
            return [];
        }

        $lines = explode("\n", $this->standardOutput);
        if ('' === end($lines)) {
            array_pop($lines);
        }

        return $lines;
    }

    public function setStandardOutput(string $standardOutput)
    {
        $this->standardOutput = $standardOutput;

        return $this;
    }

    public function setErrorOutput(string $errorOutput)
    {
        $this->errorOutput = $errorOutput;

        return $this;
    }
}
