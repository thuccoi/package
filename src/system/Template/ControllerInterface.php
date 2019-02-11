<?php

namespace system\Template;

interface ControllerInterface {

    public function __invoke($loader, $requestName, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null);
}
