<?php

namespace system\Helper;

class Str {

    //random string
    public static function rand(int $length = 32) {
        //64 / 2 = 32
        $length = $length * 2;
        $length = ($length < 4) ? 4 : $length;
        return bin2hex(random_bytes(($length - ($length % 2)) / 2));
    }

    //slugs url 
    public static function viToStr($str) {

        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );

        foreach ($unicode as $nonUnicode => $uni) {

            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }

        return $str;
    }

    //genarade username
    public static function generateUsername($string_name = "Mike Tyson", $rand_no = 200) {

        $string_name = static::viToStr($string_name);    //nonUnicode

        $username_parts = array_filter(explode(" ", strtolower($string_name))); //explode and lowercase name
        $username_parts = array_slice($username_parts, 0, 2); //return only first two arry part

        $part1 = (!empty($username_parts[0])) ? substr($username_parts[0], 0, 8) : ""; //cut first name to 8 letters
        $part2 = (!empty($username_parts[1])) ? substr($username_parts[1], 0, 5) : ""; //cut second name to 5 letters
        $part3 = ($rand_no) ? rand(0, $rand_no) : "";

        $username = $part1 . str_shuffle($part2) . $part3; //str_shuffle to randomly shuffle all characters 
        return $username;
    }

    //input array name
    public static function randAccount($arr = "") {
        if (!$arr) {
            //array name
            $arr = array("Mike", "Tyson", "Cveta", "Steeve", "Ji", "Diana", "Richard", "George", "Best", "Amber", "Leon", "Pierre", "Carden", "Kim", "Lee",
                "William", "John", "James", "Jacob", "Christopher", "Joshua", "Michael", "Jackson", "Jayden", "Ethan",
                "James", "Jacob", "Michael", "Ethan", "Tyler", "Aiden", "Joshua", "Joseph", "Noah", "Matthew",
                "Anthony", "Daniel", "Angel", "Alexander", "Jacob", "Michael", "Ethan", "Jose", "Jesus", "Joshua",
                "Jacob", "Ethan", "William", "Landon", "Joshua", "Jackson", "Aiden", "James", "Hunter", "Christopher",
                "Daniel", "Anthony", "Angel", "Jacob", "David", "Alexander", "Andrew", "Joshua", "Christopher", "Jose",
                "Alexander", "Jacob", "William", "Ethan", "Noah", "Gabriel", "Joshua", "Daniel", "Anthony", "Elijah",
                "Michael", "Ryan", "Matthew", "Alexander", "Jacob", "Christopher", "William", "Anthony", "Nicholas", "Andrew",
                "Michael", "Jacob", "William", "Christopher", "Joshua", "Jayden", "Anthony", "James", "John", "Ryan",
                "William", "Alexander", "Michael", "John", "Christopher", "Samuel", "Daniel", "Kevin", "Elijah", "James",
                "Jayden", "Joshua", "Michael", "Anthony", "Christopher", "Daniel", "Jacob", "Alexander", "Matthew", "David"
            );
        }

        $random_keys = array_rand($arr, 2);

        //account
        $first_name = $arr[$random_keys[0]];

        $last_name = $arr[$random_keys[1]];

        //username
        $username = static::generateUsername("{$first_name} {$last_name}", 10);

        //return object
        return (object) [
                    "first_name" => $first_name,
                    "last_name" => $last_name,
                    "email" => "$username@gmail.com",
                    "username" => $username
        ];
    }

}
