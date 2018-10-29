<?php

namespace system\Helper;

class String {

    //random string
    public static function rand(int $length = 32) {
        //64 / 2 = 32
        $length = $length * 2;
        $length = ($length < 4) ? 4 : $length;
        return bin2hex(random_bytes(($length - ($length % 2)) / 2));
    }

}
