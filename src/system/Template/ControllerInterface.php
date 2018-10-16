<?php

namespace system\Template;

interface ControllerInterface {

    public function __invoke($requestName, \system\Router $router, \system\Helper\Code $code, array $config = null, array $options = null);
}
