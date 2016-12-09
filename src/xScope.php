<?php namespace Rexfordge\x;

/**
    PHP will not allow us to simply call Closures that are assigned to props,
    we must employ the __call magic method to accomplish what we want.
*/
 class xScope {

    function __construct($members = array()) {
        $this->assign($members);
    }

    function assign ($members){
        foreach ($members as $name => $value) {
            $this->$name = $value;
        }
    }

    function __call($name, $args) {
        if (is_callable($this->$name)) {
            array_unshift($args, $this);
            return call_user_func_array($this->$name, $args);
        }
    }
    
}