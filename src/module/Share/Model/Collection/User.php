<?php

namespace module\Share\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(db="tami_account",collection="Users")  
 * @ODM\Indexes({
 *     @ODM\Index(keys={"username"="asc"}),
 *     @ODM\Index(keys={"email"="asc"}) 
 * }) 
 * @ODM\HasLifecycleCallbacks
 */
class User implements \module\Share\Model\Common\FieldInterface {

    //load field default
    use \module\Share\Model\Common\FieldDefault;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $username;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $password;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $first_name;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $last_name;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $image;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $phone;

    /**
     *
     * @ODM\Field(type="string") 
     */
    private $email;

    /**
     *
     * @ODM\Field(type="int")
     */
    private $email_confirm;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $token;

    /**
     *
     * @ODM\Field(type="int")
     */
    private $status;

    /**
     *
     * @ODM\ReferenceOne(targetDocument=module\Share\Model\Collection\Application::class, inversedBy="users")
     */
    private $application;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $role;

    //role
    const ROLE_OWNER = "owner";
    const ROLE_ADMIN = "admin";
    const ROLE_DEFAULT = "default";
    //status
    const STATUS_ACTIVATE = 1;
    const STATUS_DEACTIVE = -1;
    const STATUS_PENDING = 0;
    //email
    const EMAIL_CONFIRMED = 1;
    const EMAIL_PENDING = 0;

    public function __construct() {

        //init
        $this->init();

        //role default default
        $this->role = self::ROLE_DEFAULT;

        //pending status
        $this->status = self::STATUS_PENDING;

        //pending email
        $this->email_confirm = self::EMAIL_PENDING;

        //generate token
        $this->token = \system\Helper\Str::rand();
    }

    //role
    public function getRole() {
        return $this->role;
    }

    //assign role
    public function assignOwner() {
        $this->role = self::ROLE_OWNER;
        return $this;
    }

    public function assignAdmin() {
        $this->role = self::ROLE_ADMIN;
        return $this;
    }

    public function assignDefault() {
        $this->role = self::ROLE_DEFAULT;
        return $this;
    }

    //check role
    public function isOwner() {
        //role is owner
        return ($this->role == self::ROLE_OWNER);
    }

    public function isAdmin() {
        //role is admin or owner
        return ($this->isOwner() || $this->role == self::ROLE_ADMIN);
    }

    public function isDefault() {
        //role is default
        return ($this->role == self::ROLE_DEFAULT);
    }

    //application
    public function getApplication() {
        return $this->application;
    }

    public function setApplication(\module\Share\Model\Application $application) {
        $this->application = $application;
        return $this;
    }

    //send Verify email
    public function sendVerifyEmail($config) {

        $mail = new \system\Helper\Mail($config);

        $mail->to($this->email);

        $mail->subject("Bạn đã tạo tài khoản");

        //get html inline to body
        $body = $mail->inline($config['URL_ROOT'] . "/a/notify/verify?id={$this->id}&token={$this->token}", ['http://fonts.googleapis.com/css?family=Quattrocento+Sans:400,700', $config['DIR_PUBLIC'] . 'tami/css/tami.css', $config['DIR_PUBLIC'] . "css/account/notify/register.css"]);

        $mail->body($body);

        $mail->send();

        return $this;
    }

    //send confirm email
    public function sendConfirmEmail($config) {
        $this->email_confirm = self::EMAIL_CONFIRMED;

        $mail = new \system\Helper\Mail($config);

        $mail->to($this->email);

        $mail->subject("Thông tin tài khoản của bạn đã được xác nhận");

        //get html inline to body
        $body = $mail->inline($config['URL_ROOT'] . "/a/notify/confirm?id={$this->id}&token={$this->token}", ['http://fonts.googleapis.com/css?family=Quattrocento+Sans:400,700', $config['DIR_PUBLIC'] . 'tami/css/tami.css', $config['DIR_PUBLIC'] . "css/account/notify/confirm.css"]);

        $mail->body($body);

        $mail->send();

        return $this;
    }

    //function release
    public function release() {
        //object release
        $obj = $this->export();
        $obj->username = $this->getUsername();
        $obj->first_name = $this->getFirstName();
        $obj->last_name = $this->getLastName();
        $obj->name = $this->getName();
        $obj->image = $this->getImage();
        $obj->email = $this->getEmail();
        $obj->phone = $this->getPhone();
        $obj->status = $this->getStatus();
        $obj->role = $this->getRole();

        //application name
        $obj->application_name = $this->application ? $this->application->getName() : '';

        return $obj;
    }

    //status
    public function getStatus() {
        return $this->status;
    }

    //active account
    public function activate() {
        $this->status = self::STATUS_ACTIVATE;
        return $this;
    }

    //deactive account
    public function deactivate() {
        $this->status = self::STATUS_DEACTIVE;
        return $this;
    }

    //authentic
    public function authLogin($password) {
        return password_verify($password, $this->password);
    }

    //get name
    public function getName() {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    //function set and get
    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password, $config) {

        //default options password
        $conpasswd = [
            'cost' => 12,
            'salt' => 'tami_dsjhaiu4229429472r24rr34'
        ];

        //set from config
        if (isset($config['password'])) {

            $conpasswd = $config['password'];

            if (!isset($conpasswd['cost'])) {
                echo "password cost is required";
                exit;
            }

            if (!isset($conpasswd['salt'])) {
                echo "password salt is required";
                exit;
            }
        }


        //http://php.net/manual/en/function.password-hash.php
        $options = [
            'cost' => $conpasswd['cost'],
            'salt' => $conpasswd['salt']
        ];

        //hash password
        $this->password = password_hash($password, PASSWORD_BCRYPT, $options);

        return $this;
    }

    public function getFirstName() {
        return $this->first_name;
    }

    public function setFirstName($first_name) {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function setLastName($last_name) {
        $this->last_name = $last_name;
        return $this;
    }

    public function getImage() {
        return $this->image;
    }

    public function setImage($image) {
        $this->image = $image;
        return $this;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function getEmailConfirm() {
        return $this->email_confirm;
    }

    //get token
    public function getToken() {
        return $this->token;
    }

}
