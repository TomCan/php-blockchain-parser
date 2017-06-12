<?php

/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 7/06/2017
 * Time: 22:26
 */
class BlockUtils
{

    static $noLog = true;

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
        if (!BlockUtils::$noLog) {
            echo $text . "\n";
        }
    }

    static function pubkeyToAddress($pubkey) {

        $steps[0] = $pubkey;
        $steps[1] = hash("sha256", $steps[0]);
        $steps[2] = hash("ripemd160", self::hashToBytes($steps[1]));
        $steps[3] = "00" . $steps[2];
        $steps[4] = hash("sha256", self::hashToBytes($steps[3]));
        $steps[5] = hash("sha256", self::hashToBytes($steps[4]));
        $steps[6] = substr($steps[5],0,8);
        $steps[7] = $steps[3] . $steps[6];
        $steps[8] = self::bc_base58_encode(self::bc_hexdec($steps[7]));

        return "1" . $steps[8];

    }

    static function hashToBytes($hash){
        $len=strlen($hash);

        $output="";
        for ($i=0;$i<$len;$i=$i+2){
            $chr = hexdec(substr($hash,$i,2));
            $output .= chr($chr);
        }

        return $output;
    }

    // bc_ functions courtesy of Sammicth @ https://stackoverflow.com/a/19233459/2285098

    // BCmath version for huge numbers
    static function bc_arb_encode($num, $basestr) {
        if( ! function_exists('bcadd') ) {
            Throw new Exception('You need the BCmath extension.');
        }

        $base = strlen($basestr);
        $rep = '';

        while( true ){
            if( strlen($num) < 2 ) {
                if( intval($num) <= 0 ) {
                    break;
                }
            }
            $rem = bcmod($num, $base);
            $rep = $basestr[intval($rem)] . $rep;
            $num = bcdiv(bcsub($num, $rem), $base);
        }
        return $rep;
    }

    static function bc_arb_decode($num, $basestr) {
        if( ! function_exists('bcadd') ) {
            Throw new Exception('You need the BCmath extension.');
        }

        $base = strlen($basestr);
        $dec = '0';

        $num_arr = str_split((string)$num);
        $cnt = strlen($num);
        for($i=0; $i < $cnt; $i++) {
            $pos = strpos($basestr, $num_arr[$i]);
            if( $pos === false ) {
                Throw new Exception(sprintf('Unknown character %s at offset %d', $num_arr[$i], $i));
            }
            $dec = bcadd(bcmul($dec, $base), $pos);
        }
        return $dec;
    }


// base 58 alias
    static function bc_base58_encode($num) {
        return self::bc_arb_encode($num, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
    }
    static function bc_base58_decode($num) {
        return self::bc_arb_decode($num, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
    }

//hexdec with BCmath
    static function bc_hexdec($num) {
        return self::bc_arb_decode(strtolower($num), '0123456789abcdef');
    }
    static function bc_dechex($num) {
        return self::bc_arb_encode($num, '0123456789abcdef');
    }

}
