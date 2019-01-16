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
        '+', '-', '=', '&', '?', '/', ' ', '.', '@', '_',
        ':'
    ];
    private $config;
    private $dm;

//init config
    public function __construct($config, $connect) {
        $this->config = $config;
        $this->dm = $connect;
    }

    public function getConfig() {
        return $this->config;
    }

    public function getConnect() {
        return $this->dm;
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

    public function verifyTamiCode($tamicode) {
        $arr = explode(".", $tamicode);
        if (!$tamicode || count($arr) != 2) {
            return FALSE;
        }

        $member_id = $arr[0];
        $clienthash = $arr[1];

        $member = $this->dm->getRepository(\module\Share\Model\Collection\Member::class)->find($member_id);
        if (!$member) {
            return FALSE;
        }

        //make TAMI CODE on server
        $user_id = $member->getUser()->getId();
        $app_id = $member->getApp()->getId();

        $hashserver = [];
        //expire about $this->config['tamicode']['lifetime'] minutes
        for ($i = 0; $i < $this->config['tamicode']['lifetime']; $i++) {
            //last 1 minute 
            $time = date("Y-m-d H:i", time() - $i * 60);
            $hashserver[] = md5("$member_id.$user_id.$app_id.$time");
        }

        //check in array
        return in_array($clienthash, $hashserver);
    }

    public function checkTamiCode($method) {

        $tamicode = '';
        //get Tami code
        if (strtoupper($method) == \system\Helper\HTML::$TAMI_POST) {
            if (isset($_POST['__TAMI_CODE'])) {

                $tamicode = $_POST['__TAMI_CODE'];
            }
        } else if (strtoupper($method) == \system\Helper\HTML::$TAMI_GET) {
            if (isset($_GET['__TAMI_CODE'])) {

                $tamicode = $_GET['__TAMI_CODE'];
            }
        }

        if (!$this->verifyTamiCode($tamicode)) {
            $this->forbidden("Forbidden You do not have permission to access!!!");
        }

        return true;
    }

//get value on url
    public function get($name, $checkcode = true) {
        if ($checkcode === true) {
            //check tami code
            $this->checkTamiCode('GET');
        }

        $value = "";
        if (isset($_GET[$name])) {
            $value = $_GET[$name];
        }

        return $this->purify($value);
    }

//get value from method post
    public function post($name, $checkcode = true) {
        if ($checkcode === true) {
            //check tami code
            $this->checkTamiCode('POST');
        }

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

//get array data 
    public function arr($name, $method = "POST", $checkcode = true) {

        $data = [];

        if (strtoupper($method) == \system\Helper\HTML::$TAMI_POST) {
            if ($checkcode === true) {
                //check tami code
                $this->checkTamiCode('POST');
            }
            $data = $_POST[$name];
        } else if (strtoupper($method) == \system\Helper\HTML::$TAMI_GET) {

            if ($checkcode === true) {
                //check tami code
                $this->checkTamiCode('GET');
            }

            $data = $_GET[$name];
        }

        return $this->purifyArr($data);
    }

    public function purifyArr($data) {

        $result = [];

//data
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if (is_array($val)) {
                    $result[$this->purify($key)] = $this->purifyArr($val);
                } else {
                    $result[$this->purify($key)] = $this->purify($val);
                }
            }
        } else {
            return $this->purify($data);
        }

        return $result;
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
    public function release($status = 405, $message = "Error!", $data = [], $tourl = "") {
        if ($this->isFromAjax()) {
            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
            }

            echo json_encode([
                "status" => $status,
                "message" => $message,
                "data" => $data,
                "tourl" => $tourl
            ]);
            exit;
        } else {
            //rediect to router
            header("$tourl");
            exit;
        }
    }

//release error
    public function error($message = "Error!", $data = [], $tourl = "") {
        if ($tourl == "") {
            $tourl = $this->url('application', ['controller' => 'error', 'action' => 'index', 'param' => [
                    'status' => 405,
                    'message' => $message
            ]]);
        }
        $this->release(405, $message, $data, $tourl);
    }

//release success
    public function success($message = "Success!", $data = [], $tourl = "") {
        if ($tourl == "") {
            $tourl = $this->url('application', ['controller' => 'error', 'action' => 'index', 'param' => [
                    'status' => 200,
                    'message' => $message
            ]]);
        }
        $this->release(200, $message, $data, $tourl);
    }

//release notfound
    public function notfound($message = "Not Found!", $data = [], $tourl = "") {
        if ($tourl == "") {
            $tourl = $this->url('application', ['controller' => 'error', 'action' => 'index', 'param' => [
                    'status' => 404,
                    'message' => $message
            ]]);
        }
        $this->release(404, $message, $data, $tourl);
    }

//release forbidden
    public function forbidden($message = "Forbidden!", $data = [], $tourl = "") {
        if ($tourl == "") {
            $tourl = $this->url('application', ['controller' => 'error', 'action' => 'index', 'param' => [
                    'status' => 403,
                    'message' => $message
            ]]);
        }
        $this->release(403, $message, $data, $tourl);
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

//check from ajax
    public function isFromAjax() {
        if ($this->get("__TAMI_FROM_AJAX")) {
            return TRUE;
        }

        if ($this->post("__TAMI_FROM_AJAX")) {
            return TRUE;
        }

        return FALSE;
    }

//url
    public function url($module, array $options = null) {
        $layout = new \system\Template\Layout($this->config);
        return $layout->url($module, $options);
    }

    public function urlInside($module, array $options = null) {
        $layout = new \system\Template\Layout($this->config);
        return $layout->urlInside($module, $options);
    }

}
