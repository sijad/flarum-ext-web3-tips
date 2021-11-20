<?php

namespace TokenJenny\Web3Tips;

class Utils {
    static function parseUnits($num, $decimals) {
        $gmp = gmp_init(0);
        $mult = gmp_init(1);
        for ($i=strlen($num)-1;$i>=0;$i--,$mult=gmp_mul($mult, 16)) {
            $gmp = gmp_add($gmp, gmp_mul($mult, hexdec($num[$i])));
        }
        return floatval(gmp_strval($gmp)) / pow(10, $decimals);
    }
}
