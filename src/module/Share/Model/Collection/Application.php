<?php

namespace module\Share\Model;

use \Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document(
 *   db="tami_account", 
 *   collection="Applications",
 *   indexes={
 *     @Index(keys={"domain"="desc"}, options={"unique"=true}),
 *     @Index(keys={"metatype"="desc"}, options={"unique"=true})
 *   }
 * )  @ODM\HasLifecycleCallbacks
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
     * @ODM\Field(type="string") @ODM\UniqueIndex 
     */
    private $metatype;

    /**
     *
     * @ODM\Field(type="string")  @ODM\UniqueIndex 
     */
    private $domain;

    /**
     *
     * @ODM\Field(type="int")
     */
    private $status;

    public function release() {
        
    }

}
