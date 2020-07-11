<?php
/**
 * Created by PhpStorm.
 * User: mpak
 * Date: 11.07.2020
 * Time: 2:33.
 */

namespace App\Service\PhpCsFixer;

class DiffLine
{
    /** @var string */
    public $text;

    /** @var null|int Номер строки в старом файле. */
    public $oldLineNumber;

    /** @var null|int Номер строки в новом файле. */
    public $newLineNumber;

    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * Возвращает true, если строка была удалена из нового файла.
     *
     * @return bool
     */
    public function isDelete()
    {
        return 0 === mb_strpos($this->text, '-');
    }

    /**
     * Возвращает true, если строка была добавлена в новом файле.
     *
     * @return bool
     */
    public function isInsert()
    {
        return 0 === strpos($this->text, '+');
    }

    /**
     * @return bool
     */
    public function isModified()
    {
        return $this->isDelete() || $this->isInsert();
    }
}
