<?php

namespace system\Helper;

class ArrayCallback {

    public static function select($array, $callback) {
        $result = [];
        if (is_array($array) || is_object($array)) {
            foreach ($array as $key => $val) {
                $result[] = $callback($val, $key);
            }
        }
        return $result;
    }

    public static function filter($array, $callback) {
        $result = [];
        if (is_array($array) || is_object($array)) {
            foreach ($array as $key => $val) {
                $nval = $callback($val, $key);
                if ($nval) {
                    $result[] = $val;
                }
            }
        }

        return $result;
    }

    public static function find($array, $element, $callback) {
        if (is_array($array) || is_object($array)) {
            foreach ($array as $key => $obj) {
                if ($callback($obj, $element, $key)) {
                    return $obj;
                }
            }
        }

        return null;
    }

    public static function findById($array, $id, $callback = null) {
        if ($callback) {
            return static::find($array, $id, $callback);
        }

        if (is_array($array) || is_object($array)) {
            foreach ($array as $key => $obj) {
                if ($obj->id == $id) {
                    return $obj;
                }
            }
        }

        return null;
    }

    public static function render($array, $callback) {
        $html = '';

        if (is_array($array) || is_object($array)) {
            foreach ($array as $key => $val) {
                $html .= $callback($val, $key);
            }
        }

        return $html;
    }

}
