<?php

namespace system\Helper;

class Code {

    private $charaters = [
        'a', 'ă', 'â',
        'ạ', 'ặ', 'ậ',
        'ã', 'ẵ', 'ẫ',
        'ả', 'ẳ', 'ẩ',
        'á', 'ắ', 'ấ',
        'à', 'ằ', 'ầ',
        'b',
        'c',
        'd', 'đ',
        'e', 'ê',
        'ẹ', 'ệ',
        'ẽ', 'ễ',
        'ẻ', 'ể',
        'é', 'ế',
        'è', 'ề',
        'f',
        'g',
        'h',
        'i',
        'ị',
        'ĩ',
        'ỉ',
        'í',
        'ì',
        'j',
        'k',
        'l',
        'm',
        'n',
        'o', 'ô', 'ơ',
        'õ', 'ỗ', 'ỡ',
        'ỏ', 'ổ', 'ở',
        'ó', 'ố', 'ớ',
        'ọ', 'ộ', 'ợ',
        'ò', 'ồ', 'ờ',
        'p',
        'q',
        'l',
        'r',
        's',
        't',
        'u', 'ư',
        'ũ', 'ữ',
        'ủ', 'ử',
        'ụ', 'ự',
        'ú', 'ứ',
        'ù', 'ừ',
        'v',
        'x',
        'y',
        'ỵ',
        'ỹ',
        'ỷ',
        'ý',
        'ỳ',
        'z',
        'w',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        '+', '-', '=', '&', '?', '/', ' ', '.', '@', '_'
    ];
    private $config;

    //init config
    public function __construct($config) {
        $this->config = $config;
    }

    //data input inline
    public function inputInline($name, $method = "GET") {

        $result = "";
        //switch method
        switch ($method) {
            case \system\Helper\HTML::$TAMI_GET:
                $result = $this->get($name);
                break;
            case \system\Helper\HTML::$TAMI_POST:
                $result = $this->post($name);
                break;
        }


        return $result;
    }

    //get value on url
    public function get($name) {
        $value = "";
        if (isset($_GET[$name])) {
            $value = $_GET[$name];
        }

        return $this->purify($value);
    }

    //get value form method post
    public function post($name) {
        $value = "";
        if (isset($_POST[$name])) {
            $value = $_POST[$name];
        } else {

            //'contentType': 'application/json'
            $POSTdata = json_decode(file_get_contents("php://input"), true);

            if (isset($POSTdata[$name])) {
                $value = $POSTdata[$name];
            }
        }

        return $this->purify($value);
    }

    //clean data
    public function purify($value) {

        $result = '';

        //get length of value string
        $len = strlen($value);
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($value, $i, 1);
            //purify all char
            if (in_array(mb_strtolower($char), $this->charaters)) {
                $result = $result . $char;
            }
        }

        return $result;
    }

    //release ajax
    public function release($status = 405, $message = "Error!", $data = [], \system\Router $router = null) {
        //page router
        if (!$router) {
            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
            }

            echo json_encode([
                "status" => $status,
                "message" => $message,
                "data" => $data
            ]);
            exit;
        }

        if (!isset($this->config['routerError'])) {
            echo 'Code Not found config routerError';
            exit;
        }

        if (!isset($this->config['routerError']['module'])) {
            echo 'Code Not found config module routerError';
            exit;
        }

        if (!isset($this->config['routerError']['controller'])) {
            echo 'Code Not found config controller routerError';
            exit;
        }

        if (!isset($this->config['routerError']['action'])) {
            echo 'Code Not found config action routerError';
            exit;
        }

        //redirect to page error
        $router->redirect($this->config['routerError']['module'], [
            'controller' => $this->config['routerError']['controller'],
            'action' => $this->config['routerError']['action'],
            'param' => [
                'status' => $status,
                'message' => $message
            ]
        ]);
    }

    //release error
    public function error($message = "Error!", $data = [], \system\Router $router = null) {
        $this->release(405, $message, $data, $router);
    }

    //release success
    public function success($message = "Success!", $data = [], \system\Router $router = null) {
        $this->release(200, $message, $data, $router);
    }

    //release notfound
    public function notfound($message = "Not Found!", $data = [], \system\Router $router = null) {
        $this->release(404, $message, $data, $router);
    }

    //release forbidden
    public function forbidden($message = "Forbidden!", $data = [], \system\Router $router = null) {
        $this->release(403, $message, $data, $router);
    }

    //pretty varial
    public static function debug($obj, $dump = false) {
        echo "<pre>";
        if (!$dump) {
            print_r($obj);
        } else {
            var_dump($obj);
        }
        exit;
    }

}
