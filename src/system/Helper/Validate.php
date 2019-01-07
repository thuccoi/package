<?php

namespace system\Helper;

class Validate {

    //check validate email
    public static function isEmail(&$email) {
        //isset
        if (self::isEmpty($email)) {
            return false;
        }

        //check string
        if (!self::isString($email)) {
            return false;
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

    public static function isInt(&$num) {
        //isset
        if (!isset($num)) {
            return false;
        }

        return is_int($num);
    }

    public static function isFloat(&$num) {
        //isset
        if (!isset($num)) {
            return false;
        }

        return is_float($num);
    }

    public static function isBool(&$boolen) {
        //isset
        if (!isset($boolen)) {
            return false;
        }

        return is_bool($boolen);
    }

    public static function isArray(&$arr) {
        //isset
        if (!isset($arr)) {
            return false;
        }

        return is_array($arr);
    }

    public static function isObject(&$obj) {
        //isset
        if (!isset($obj)) {
            return false;
        }

        return is_object($obj);
    }

    //check is string
    public static function isString(&$str) {
        //isset
        if (!isset($str)) {
            return false;
        }

        return is_string($str);
    }

    public static function isEmpty(&$obj) {
        //check isset
        if (!isset($obj)) {
            return true;
        }

        //check null
        if ($obj === null) {
            return true;
        }
        
        //check empty
        if (empty($obj)) {
            return true;
        }

        //check empty string
        if ($obj === "") {
            return true;
        }

        return false;
    }

}
