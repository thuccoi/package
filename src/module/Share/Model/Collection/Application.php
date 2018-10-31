<?php

namespace module\Share\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(
 *   db="tami_account", 
 *   collection="Applications"
 * )  
 * @ODM\Indexes({
 *     @ODM\Index(keys={"metatype"="asc"}),
 *     @ODM\Index(keys={"domain"="asc"}) 
 * }) 
 * @ODM\HasLifecycleCallbacks
 */
class Application implements \module\Share\Model\Common\FieldInterface {

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
     * @ODM\ReferenceMany(targetDocument=module\Share\Model\Collection\User::class, mappedBy="application")
     */
    private $users;

    //users
    public function getUsers() {
        return $this->users;
    }

    //get list owners of application
    public function getOwners() {
        $owners = [];
        if ($this->users) {
            foreach ($this->users as $val) {
                //check role is owner
                if ($val->isOwner()) {
                    $owners = $val;
                }
            }
        }

        return $owners;
    }

    //get list admins of application
    public function getAdmins() {
        $admins = [];
        if ($this->users) {
            foreach ($this->users as $val) {
                //check role is admin
                if ($val->isAdmin()) {
                    $admins = $val;
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
