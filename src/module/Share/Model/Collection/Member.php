<?php

namespace module\Share\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="Members", db="tami_account")
 * @ODM\HasLifecycleCallbacks
 */
class Member extends \module\Share\Model\Common\AbsField {

    use \module\Share\Model\Common\FieldDefault;

    /**
     *
     * @ODM\ReferenceOne(targetDocument=module\Share\Model\Collection\App::class, inversedBy="members")
     */
    private $app;

    /**
     *
     * @ODM\ReferenceOne(targetDocument=module\Share\Model\Collection\User::class, inversedBy="members")
     */
    private $user;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $role;

    //role
    const ROLE_OWNER = "owner";
    const ROLE_ADMIN = "admin";
    const ROLE_DEFAULT = "default";

    public function __construct(\module\Share\Model\Collection\App $app, \module\Share\Model\Collection\User $user) {
        $this->init();
        $this->app = $app;
        $this->user = $user;


        //role default default
        $this->role = self::ROLE_DEFAULT;
    }

    //add app
    public function setApp(\module\Share\Model\Collection\App $app) {
        $this->app = $app;
        return $this;
    }

    //add user
    public function setUser(\module\Share\Model\Collection\User $user) {
        $this->user = $user;
        return $this;
    }

    //role
    public function getRole() {
        return $this->role;
    }

    //assign role
    public function assignOwner($config) {
        $this->role = self::ROLE_OWNER;

        //new token
        $this->token = \system\Helper\Str::rand();

        $mail = new \system\Helper\Mail($config);

        $mail->to($this->email);

        $mail->subject("Bạn được giao vai trò quản trị cho ứng dụng: {$this->app->getName()}");

        //get html inline to body
        $body = $mail->inline($config['URL_ROOT'] . "/a/notify/member-owner?id={$this->id}&token={$this->token}", ['http://fonts.googleapis.com/css?family=Quattrocento+Sans:400,700', $config['DIR_PUBLIC'] . 'tami/css/tami.css', $config['DIR_PUBLIC'] . "css/account/notify/member/owner.css"]);

        $mail->body($body);

        $mail->send();

        return $this;
    }

    public function assignAdmin($config) {
        $this->role = self::ROLE_ADMIN;

        //new token
        $this->token = \system\Helper\Str::rand();

        $mail = new \system\Helper\Mail($config);

        $mail->to($this->email);

        $mail->subject("Bạn được giao vai trò quản lý cho ứng dụng: {$this->app->getName()}");

        //get html inline to body
        $body = $mail->inline($config['URL_ROOT'] . "/a/notify/member-admin?id={$this->id}&token={$this->token}", ['http://fonts.googleapis.com/css?family=Quattrocento+Sans:400,700', $config['DIR_PUBLIC'] . 'tami/css/tami.css', $config['DIR_PUBLIC'] . "css/account/notify/member/admin.css"]);

        $mail->body($body);

        $mail->send();

        return $this;
    }

    public function assignDefault($config) {
        $this->role = self::ROLE_DEFAULT;
        
        //new token
        $this->token = \system\Helper\Str::rand();

        $mail = new \system\Helper\Mail($config);

        $mail->to($this->email);

        $mail->subject("Bạn được giao vai trò thành viên cho ứng dụng: {$this->app->getName()}");

        //get html inline to body
        $body = $mail->inline($config['URL_ROOT'] . "/a/notify/member-default?id={$this->id}&token={$this->token}", ['http://fonts.googleapis.com/css?family=Quattrocento+Sans:400,700', $config['DIR_PUBLIC'] . 'tami/css/tami.css', $config['DIR_PUBLIC'] . "css/account/notify/member/default.css"]);

        $mail->body($body);

        $mail->send();
        
        return $this;
    }

    //check role
    public function isOwner() {
        //role is owner
        return ($this->role == self::ROLE_OWNER);
    }

    public function isAdmin() {
        //role is admin or owner
        return ($this->role == self::ROLE_ADMIN);
    }

    public function isDefault() {
        //role is default
        return ($this->role == self::ROLE_DEFAULT);
    }

    public function release() {
        $obj = $this->export();
        $obj->role = $this->getRole();
        return $obj;
    }

}
