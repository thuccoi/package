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

    /**
     *
     * @ODM\Field(type="int")
     */
    private $status;

    //role
    const ROLE_OWNER = "owner";
    const ROLE_ADMIN = "admin";
    const ROLE_DEFAULT = "default";
    //status
    const STATUS_ACTIVATE = 1;
    const STATUS_DEACTIVE = -1;
    const STATUS_PENDING = 0;

    public function __construct(\module\Share\Model\Collection\App $app, \module\Share\Model\Collection\User $user) {
        $this->init();
        $this->app = $app;
        $this->user = $user;


        //role default default
        $this->role = self::ROLE_DEFAULT;


        //pending status
        $this->status = self::STATUS_PENDING;
    }

    //set viewer session
    public function setViewer($session) {
        //set session
        $session->set("auth", 1);
        $session->set("app", $this->app);
        $session->set("user", $this->user);
        $session->set("member", $this);
    }

    //add app
    public function setApp(\module\Share\Model\Collection\App $app) {
        $this->app = $app;
        return $this;
    }

    public function getApp() {
        return $this->app;
    }

    //add user
    public function setUser(\module\Share\Model\Collection\User $user) {
        $this->user = $user;
        return $this;
    }

    public function getUser() {
        return $this->user;
    }

    //role
    public function getRole() {
        return $this->role;
    }

    //activate member
    //send activate email
    public function sendActivateEmail($member, $config) {

        $mail = new \system\Helper\Mail($config);

        $mail->to($this->user->getEmail());

        $mail->subject("Kích hoạt thành viên mới trong ứng dụng của bạn");

        //get html inline to body
        $body = $mail->inline($config['URL_ROOT'] . "/a/notify/admin-activate?id={$member->getId()}&token={$member->getToken()}", ['http://fonts.googleapis.com/css?family=Quattrocento+Sans:400,700', $config['DIR_PUBLIC'] . 'tami/css/tami.css', $config['DIR_PUBLIC'] . "css/account/notify/admin/activate.css"]);

        $mail->body($body);

        $mail->send();

        return $this;
    }

    //assign role
    public function assignOwner($config) {
        $this->role = self::ROLE_OWNER;

        $mail = new \system\Helper\Mail($config);

        $mail->to($this->user->getEmail());

        $mail->subject("Bạn được giao vai trò quản trị cho ứng dụng: {$this->app->getName()}");

        //get html inline to body
        $body = $mail->inline($config['URL_ROOT'] . "/a/notify/member-owner?id={$this->id}&token={$this->token}", ['http://fonts.googleapis.com/css?family=Quattrocento+Sans:400,700', $config['DIR_PUBLIC'] . 'tami/css/tami.css', $config['DIR_PUBLIC'] . "css/account/notify/member/owner.css"]);

        $mail->body($body);

        $mail->send();

        return $this;
    }

    public function assignAdmin($config) {
        $this->role = self::ROLE_ADMIN;

        $mail = new \system\Helper\Mail($config);

        $mail->to($this->user->getEmail());

        $mail->subject("Bạn được giao vai trò quản lý cho ứng dụng: {$this->app->getName()}");

        //get html inline to body
        $body = $mail->inline($config['URL_ROOT'] . "/a/notify/member-admin?id={$this->id}&token={$this->token}", ['http://fonts.googleapis.com/css?family=Quattrocento+Sans:400,700', $config['DIR_PUBLIC'] . 'tami/css/tami.css', $config['DIR_PUBLIC'] . "css/account/notify/member/admin.css"]);

        $mail->body($body);

        $mail->send();

        return $this;
    }

    public function assignDefault($config) {
        $this->role = self::ROLE_DEFAULT;

        $mail = new \system\Helper\Mail($config);

        $mail->to($this->user->getEmail());

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
        $obj->status = $this->getStatus();
        $obj->token = $this->getToken();
        return $obj;
    }

    //status
    public function getStatus() {
        return $this->status;
    }

    //check status
    public function isActivate() {
        return ($this->status == self::STATUS_ACTIVATE);
    }

    public function isDeactivate() {
        return ($this->status == self::STATUS_DEACTIVE);
    }

    //active account
    public function activate($config) {
        $this->status = self::STATUS_ACTIVATE;

        $mail = new \system\Helper\Mail($config);

        $mail->to($this->user->getEmail());

        $mail->subject("Tài khoản thành viên của bạn đã được kích hoạt trong ứng dụng: {$this->app->getName()}");

        //get html inline to body
        $body = $mail->inline($config['URL_ROOT'] . "/a/notify/member-activate?id={$this->id}&token={$this->token}", ['http://fonts.googleapis.com/css?family=Quattrocento+Sans:400,700', $config['DIR_PUBLIC'] . 'tami/css/tami.css', $config['DIR_PUBLIC'] . "css/account/notify/member/activate.css"]);

        $mail->body($body);

        $mail->send();

        return $this;
    }

    //deactive account
    public function deactivate($config) {
        $this->status = self::STATUS_DEACTIVE;

        $mail = new \system\Helper\Mail($config);

        $mail->to($this->user->getEmail());

        $mail->subject("Tài khoản thành viên của bạn đã bị từ chối hoạt động trong ứng dụng: {$this->app->getName()}");

        //get html inline to body
        $body = $mail->inline($config['URL_ROOT'] . "/a/notify/member-deactivate?id={$this->id}&token={$this->token}", ['http://fonts.googleapis.com/css?family=Quattrocento+Sans:400,700', $config['DIR_PUBLIC'] . 'tami/css/tami.css', $config['DIR_PUBLIC'] . "css/account/notify/member/deactivate.css"]);

        $mail->body($body);

        $mail->send();

        return $this;
    }

}
