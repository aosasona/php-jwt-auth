<?php

namespace Trulyao\PhpJwt\Utils;

class InputHandler
{
    public static function normalizeString($string): string
    {
        return trim(htmlspecialchars($string));
    }

}