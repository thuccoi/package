<?php

namespace module\Assignment\Model\Collection;

/**
 * @ODM\Document(
 *   db="tami_assignment", 
 *   collection="Roles"
 * )  
 */
class Role extends \module\Share\Model\Common\AbsField {

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
     * @ODM\ReferenceMany(targetDocument="module\Assignment\Model\Collection\Role", mappedBy="children")
     */
    private $parent;

    /**
     * @ODM\ReferenceMany(targetDocument="module\Assignment\Model\Collection\Role", inversedBy="parent")
     */
    private $children;

    /** @ODM\ReferenceMany(targetDocument="module\Assignment\Model\Collection\PermissionToRole", mappedBy="role") */
    private $permissions;

    /** @ODM\ReferenceMany(targetDocument="module\Assignment\Model\Collection\RoleToMember", mappedBy="role") */
    private $members;

    public function __construct() {
        $this->init();

        $this->parent = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->permissions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->members = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function addChildren(\module\Assignment\Model\Collection\Role $obj) {
        $this->children[] = $obj;
        $this->parent[] = $this;
        return $this;
    }

    //romve children
    public function removeChildren($children_id) {

        unset($this->children[$children_id]);

        return $this;
    }

    //permission
    public function getPermissions() {
        //get all children and sub children
        $arr = [];
        foreach ($this->permissions as $val) {
            $arr[] = $val->getPermission();
        }

        return $arr;
    }

    //member
    public function getMembers() {
        $arr = [];
        foreach ($this->members as $val) {
            $arr[] = $val->getMemberId();
        }
        return $arr;
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

    public function release() {
        $obj = $this->export();
        $obj->name = $this->getName();
        $obj->metatype = $this->getMetatype();

        return $obj;
    }

}
