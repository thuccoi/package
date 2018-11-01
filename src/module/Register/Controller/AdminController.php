<?php

namespace module\Register;

class AmindController extends \system\Template\AbstractController {

    //entity user
    protected $entity;

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);

        //init entity
        $this->entity = new \module\Share\Model\Entity\User($connect, $code, $config);
    }
    
    //activate member
    public function activateAction(){
        
    }
    
    //activate member
    public function deactivateAction(){
        
    }
    
    
   
}
