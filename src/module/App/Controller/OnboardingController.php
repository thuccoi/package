<?php

namespace module\App\Controller;

class OnboardingController extends \system\Template\AbstractController {

    public function __construct($connect, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {
        parent::__construct($connect, $router, $code, $session, $config, $options);
    }

    public function indexAction() {
        //view dir
        $this->setViewDir(dirname(__DIR__) . '/View/');
        return [
        ];
    }

}
