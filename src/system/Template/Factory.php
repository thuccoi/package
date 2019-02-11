<?php

namespace system\Template;

final class Factory implements \system\Template\ControllerInterface {

    public function __invoke($connect, $requestName, \system\Router $router, \system\Helper\Code $code, \system\Session $session, array $config = null, array $options = null) {

        return new $requestName($connect, $router, $code, $session, $config, $options);
    }

}
