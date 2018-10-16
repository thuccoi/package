<?php

namespace system\Template;

final class Factory implements \system\Template\ControllerInterface {

    public function __invoke($requestName, \system\Router $router, \system\Helper\Code $code, array $config = null, array $options = null) {

        return new $requestName($router, $code, $config, $options);
    }

}
