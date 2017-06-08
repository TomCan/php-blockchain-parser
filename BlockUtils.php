<?php

/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 7/06/2017
 * Time: 22:26
 */
class BlockUtils
{

    static function hexToNumber($bytes) {

        self::Log("hexToNumber " . bin2hex($bytes));

        $value = 0;
        for ($i = 0; $i < strlen($bytes); $i++) {
            $value += ord($bytes{$i}) * (256 ** $i);
        }

        return $value;

    }

    static function varInt($data, &$position = 0) {

        if ($position > strlen($data)) {
            die("varInt position larger than datalength");
        }

        $first = $data{$position};
        if (ord($first) < 253) {
            $position += 1;
            return ord($first);
        } elseif (ord($first) == 253) {
            $position += 3;
            return self::hexToNumber(substr($data, $position - 2, 2));
        } elseif (ord($first) == 254) {
            $position += 5;
            return self::hexToNumber(substr($data, $position - 4, 4));
        } elseif (ord($first) == 255) {
            $position += 9;
            return self::hexToNumber(substr($data, $position - 8, 8));
        }

    }

    static function Log($text) {
        echo $text . "\n";
    }

}