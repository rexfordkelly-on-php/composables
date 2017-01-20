<?php namespace Rexfordge\x;

/**
    PHP will not allow us to simply call Closures that are assigned to props,
    we must employ the __call magic method to accomplish what we want.
*/
 class xContext {

    function __construct($members = array()) {
        $this->assign($members);
    }

    function last (){
        $cnt = count(get_object_vars($this)) -1;
        if( $this->{$cnt} ) return $this->{$cnt};
        return NULL;
    }

    function instance (){
        return $this;
    }
    
    function each ($func){
        foreach (get_object_vars($this) as $key => $value) {
            call_user_func($func, $value, $key, $this );
        }
    }

    function map ($func, $curry = NULL ){
        $curry = $curry ? $curry : new Context();
        foreach (get_object_vars($this) as $key => $value) {
            $curry = call_user_func($func, $curry, $value, $key, $this );
        }
        return $curry;
    }

    function assign ($members, $value = NULL ){
        if(is_array($members)){
            foreach ($members as $name => $value) {
                $this->$name = $value;
            }
        } else {
            $this->{$members} = $value;
        }
    }

    function __call ($name, $args) {
        if (is_callable($this->$name)) {
            array_unshift($args, $this);
            return call_user_func_array($this->$name, $args);
        }
    }
    
}