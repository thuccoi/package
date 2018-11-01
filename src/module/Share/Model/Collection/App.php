<?php

namespace module\Share\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(
 *   db="tami_account", 
 *   collection="Apps"
 * )  
 * @ODM\HasLifecycleCallbacks
 */
class App extends \module\Share\Model\Common\AbsField {

    use \module\Share\Model\Common\FieldDefault;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $name;

    /**
     *
     * @ODM\Field(type="string")
     */
    private $metatype;

    /**
     *
     * @ODM\Field(type="string") 
     */
    private $domain;

    /**
     *
     * @ODM\ReferenceMany(targetDocument=module\Share\Model\Collection\Member::class, mappedBy="app")
     */
    private $members;

    //members
    public function getMembers() {
        return $this->members;
    }

    //get list owners of app
    public function getOwners() {
        $owners = [];
        if ($this->members) {
            foreach ($this->members as $val) {
                //check role is owner
                if ($val->isOwner()) {
                    $owners[] = $val;
                }
            }
        }

        return $owners;
    }

    //get list admins of app
    public function getAdmins() {
        $admins = [];
        if ($this->members) {
            foreach ($this->members as $val) {
                //check role is admin
                if ($val->isAdmin()) {
                    $admins[] = $val;
                }
            }
        }

        return $admins;
    }

    //name
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    //metatype
    public function getMetatype() {
        return $this->metatype;
    }

    public function setMetatype($metatype) {
        $this->metatype = $metatype;
        return $this;
    }

    //domain
    public function getDomain() {
        return $this->domain;
    }

    public function setDomain($domain) {
        $this->domain = $domain;
        return $this;
    }

    public function release() {
        //get export
        $obj = $this->export();
        $obj->name = $this->getName();
        $obj->metatype = $this->getMetatype();
        $obj->domain = $this->getDomain();

        return $obj;
    }

}
