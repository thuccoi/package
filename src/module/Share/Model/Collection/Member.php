<?php

namespace module\Share\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(collection="Members", db="tami_account")
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
     * @ODM\ReferenceMany(targetDocument=module\Assignment\Model\Collection\RoleToMember::class, mappedBy="member")
     */
    private $roles;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $title;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $alias;

    /**
     *
     * @ODM\Field(type="int")
     */
    private $status;

    /**
     * @ODM\ReferenceOne(targetDocument="module\Share\Model\Collection\Member", inversedBy="employees")
     */
    private $manager;

    /**
     * @ODM\ReferenceMany(targetDocument="module\Share\Model\Collection\Member", mappedBy="manager")
     */
    private $employees;

    //status
    const STATUS_ACTIVATE = 1;
    const STATUS_DEACTIVE = -1;
    const STATUS_PENDING = 0;

    public function __construct(\module\Share\Model\Collection\App $app, \module\Share\Model\Collection\User $user) {
        $this->init();
        $this->app = $app;
        $this->user = $user;

        //activate status
        $this->status = self::STATUS_ACTIVATE;


        $this->employees = new \Doctrine\Common\Collections\ArrayCollection();
    }

    //manager
    public function setManager(\module\Share\Model\Collection\Member $obj) {
        $this->employees[] = $this;
        $this->manager = $obj;
        return $this;
    }

    public function getManager() {
        return $this->manager;
    }

    //employees
    public function getEmployees() {
        return $this->employees;
    }

    public function removeEmployee($employee_id) {

        unset($this->employees[$employee_id]);

        return $this;
    }

    //set viewer session
    public function setViewer($session, $config) {
        //set session
        $session->set("auth", 1);

        $session->set("app", $this->getApp()->release());

        //onboarding
        $apponboarding = $this->getApp()->getOnboarding();
        $arrold = \system\Helper\ArrayCallback::select($apponboarding, function($e) {
                    return $e['metatype'];
                });

        $sysonboarding = $config['app_onboarding'];
        $arrnew = \system\Helper\ArrayCallback::select($sysonboarding, function($e) {
                    return $e['metatype'];
                });
                
        $nonboard = [];
        //onboarding addnew 
        foreach ($sysonboarding as $val) {
            if (!in_array($val['metatype'], $arrold)) {
                $nonboard[] = [
                    'metatype' => $val['metatype'],
                    'status'   => 0
                ];
            }
        }

        //onboarding add old
        foreach ($apponboarding as $val) {
            if (in_array($val['metatype'], $arrnew)) {
                $nonboard[] = $val;
            }
        }

        $session->set("app_onboarding", $nonboard);

        //user
        $session->set("user", $this->getUser()->release());
        
        //member
        $session->set("member", $this->release());

        //set permission
        $permissions = [];
        if ($this->roles) {
            foreach ($this->roles as $val) {

                foreach ($val->getRole()->getAllPermissions() as $per) {

                    $permissions[] = $per;
                }
            }
        }

        //set allowed_actions
        //action array
        $peraction = [];
        foreach ($config['account_member']['permissions'] as $val) {
            $peraction[$val['value']] = $val['action'];
        }

        $allowed_actions = [];
        foreach (array_unique($permissions) as $per) {
            if (isset($peraction[$per])) {
                foreach ($peraction[$per] as $val) {
                    $allowed_actions[] = $val;
                }
            }
        }

        $session->set("allowed_actions", array_unique($allowed_actions));

        return $this;
    }

    //roles
    public function getRoles() {
        return $this->roles;
    }

    //title
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    //alias
    public function setAlias($alias) {
        $this->alias = $alias;
        return $this;
    }

    public function getAlias() {
        return $this->alias;
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

    public function release() {
        $obj = $this->export();

        $obj->title = $this->getTitle();
        $obj->alias = $this->getAlias();
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
    public function activate() {
        $this->status = self::STATUS_ACTIVATE;

        return $this;
    }

    //deactive account
    public function deactivate() {
        $this->status = self::STATUS_DEACTIVE;

        return $this;
    }

}
