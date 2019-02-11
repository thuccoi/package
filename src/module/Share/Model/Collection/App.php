<?php

namespace module\Share\Model\Collection;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(
 *   db="tami_account", 
 *   collection="Apps"
 * )  
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
    private $image;

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
     * @ODM\ReferenceMany(
     *  targetDocument=module\Share\Model\Collection\Member::class, 
     *  mappedBy="app",
     *  sort={"create_at"="desc"}
     * )
     */
    private $members;

    //members
    public function getMembers() {
        $members = [];
        foreach ($this->members as $val) {
            if (!$val->getDeletedAt()) {
                $members [] = $val;
            }
        }
        return $members;
    }

    //members was deleted
    public function getMembersWD() {
        $members = [];
        foreach ($this->members as $val) {
            if ($val->getDeletedAt()) {
                $members [] = $val;
            }
        }
        return $members;
    }


    //name
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    //image
    public function getImage() {
        return $this->image;
    }

    public function setImage($image) {
        $this->image = $image;
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
        $obj->image = $this->getImage();
        $obj->metatype = $this->getMetatype();
        $obj->domain = $this->getDomain();

        return $obj;
    }

}
