<?php

namespace App\Tools;

class TypeCaster
{
    /**
     * @param mixed $value
     */
    public static function bool($value): ?bool
    {
        if (null === $value) {
            return null;
        }

        return (bool) $value;
    }

    /**
     * @param mixed $value
     */
    public static function int($value): ?int
    {
        if (null === $value) {
            return null;
        }

        return (int) $value;
    }

    /**
     * @param mixed $value
     */
    public static function string($value): ?string
    {
        if (null === $value) {
            return null;
        }
        $value = trim((string) $value);
        if ('' === $value) {
            return null;
        }

        return $value;
    }

    /**
     * @param mixed $value
     */
    public static function id($value): ?int
    {
        if (empty($value)) {
            return null;
        }

        return static::int($value);
    }

    /**
     * @param mixed $value
     */
    public static function date($value): ?string
    {
        if (empty($value) || !strtotime($value) || false !== strpos($value, '0000-00-00')) {
            return null;
        }

        return date('Y-m-d', strtotime($value));
    }

    /**
     * @param mixed $value
     */
    public static function datetime($value): ?string
    {
        if (empty($value) || false !== strpos($value, '0000-00-00')) {
            return null;
        }
        // Если на входе число, считаем его как timestamp и его не надо преобразовывать
        if (is_int($value)) {
            return date('c', $value);
        }
        $value = strtotime($value);
        if (false === $value) {
            return null;
        }

        return date('c', $value);
    }
}
