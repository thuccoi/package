<?php

namespace system\Queue;

trait TraitDispatch {

    public static function dispatch() {
        return new Queue(get_class(new static()), func_get_args());
    }

}
