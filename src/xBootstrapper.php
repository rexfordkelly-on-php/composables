<?php namespace Rexfordge\x;

class xBootstrapper {
    static function register($path){
    	$path = __DIR__ . '/_' . strtolower($path) . '.php';
    	if( file_exists($path) ){
			@require_once $path;    		
    	} else {
    		return false;
    	}
    }
}