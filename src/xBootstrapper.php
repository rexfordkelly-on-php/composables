<?php namespace Rexfordge\x;

/**
	A stupid simple workaround to not being able to autoload 
	function via composer.
*/
class xBootstrapper {
    static function register($path, $root_path = __DIR__ ){
    	$path = $root_path . '/_' . strtolower($path) . '.php';
    	if( file_exists($path) ){
			require_once $path;    		
    	} else {
    		return false;
    	}
    }
}