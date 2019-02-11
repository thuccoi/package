<?php

namespace module\Login\Controller;

class LogoutController extends \system\Template\AbstractController {

    public function indexAction() {

        //destroy session
        $this->getSession()->destroy();

        //get home
        $home = $this->getConfig('home');

        $this->getRouter()->redirect($home['module'], ['controller' => $home['controller'], 'action' => $home['action']]);
    }

}
