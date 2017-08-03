<?php

namespace SiteWideColoring\App\Utility;

final class ArrayUtil
{
    public static function isAssociative(array $array)
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }
}