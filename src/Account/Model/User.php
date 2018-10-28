<?php

namespace Account\Model;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(db="tami_account", collection="Users")
 * 
 * @ODM\Indexes({
 *      @ODM\Index(keys={"username"="desc"}, options={"unique"=true})
 * })
 * 
 * @ODM\HasLifecycleCallbacks
 */
class User extends AbsField {

    //load field default
    use FieldDefault;

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
     * @ODM\Field(type="string")  @ODM\UniqueIndex 
     */
    private $email;

    /**
     * @ODM\PostPersist
     */
    public function sendWelcomeEmail() {
        
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

        return $obj;
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

        //http://php.net/manual/en/function.password-hash.php
        $options = [
            'cost' => 12,
            'salt' => 'tami_875DSDEW23232@fdfd43iyyrtr'
        ];

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

}