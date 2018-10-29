<?php

namespace module\Share\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(db="tami_account", collection="Users")  @ODM\HasLifecycleCallbacks
 */
class User extends \module\Share\Model\Common\AbsField {

    //load field default
    use \module\Share\Model\Common\FieldDefault;

    /**
     *
     * @ODM\Field(type="string")  @ODM\UniqueIndex 
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
     * @ODM\Field(type="string")  @ODM\UniqueIndex 
     */
    private $phone;

    /**
     *
     * @ODM\Field(type="string")  @ODM\UniqueIndex 
     */
    private $email;

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

    const STATUS_ACTIVATE = 1;
    const STATUS_DEACTIVE = -1;
    const STATUS_PENDING = 0;

    public function __construct($config) {
        parent::__construct($config);
        //pending status
        $this->status = self::STATUS_PENDING;

        //generate token
        $this->token = \system\Helper\Str::rand();
    }

    /**
     * @ODM\PreFlush
     */
    public function preFlush(\Doctrine\ODM\MongoDB\Event\PreFlushEventArgs $eventArgs) {

        $mail = new \system\Helper\Mail($this->getTamiConfig());

        $mail->to($this->email);

        $mail->subject("Bạn đã tạo tài khoản");

        $body = file_get_contents($this->getTamiConfig()['DOMAIN'] . "/a/email/activate?id={$this->email}&token={$this->token}");
        
        $mail->body($body);

        $mail->send();
    }

    /**
     *  @ODM\PostRemove
     */
    public function postRemove() {
        // ...
    }

    /**
     * @ODM\PostUpdate 
     */
    public function postUpdate() {
        // ...
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

    public function setPassword($password) {

        //default options password
        $conpasswd = [
            'cost' => 12,
            'salt' => 'tami_dsjhaiu4229429472r24rr34'
        ];

        //set from config
        if (isset($this->getTamiConfig()['password'])) {

            $conpasswd = $this->getTamiConfig()['password'];

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

    //get token
    public function getToken() {
        return $this->token;
    }

}
