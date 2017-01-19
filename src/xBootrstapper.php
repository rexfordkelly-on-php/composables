<?php namespace Rexfordge\x;

class xBootstrapper {
    static private $initialized = false;
    static function initialize(){
        if(!self::initialized){
            require_once __DIR__ . '/_facades.php';
            self::initialized = true;
        }
        return self::initialized;
    }
}