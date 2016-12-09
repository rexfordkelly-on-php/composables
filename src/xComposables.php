<?php namespace Rexfordge\x;

/**
    To be able to autoload our library, via composer, we need to 
    encapsulate our functionality within the context of a Class.
*/
class xComposable {

    /**
        Simple helper to make including/requiring config files 
        fluent. load_configs will return and array.
    */
    static function load_configs( $path, $preserve = true ) {
        $tmp = require $path;
        return $preserve ? parse_and_preserve_refs($tmp, false) : parse_configs($tmp);
    }


    /**
        Simple helper to make including/requiring config files 
        fluent, mount_configs will return an object
    */
    static function mount_configs( $path ) {
        $tmp = require $path;
        return parse_and_preserve_refs($tmp);
    }


    /**
        Helper to preserve and enhance references and closures.
    */
    static function parse_and_preserve_refs( $configs, $asObject = true){
        # collect all strings to parse
        $simples = []; 
        # collect all closure to bind once completed.
        $closures = [];
        
        foreach ($configs as $key => $value) 
            if(!is_callable($value) && !is_object($value) && !is_array($value)) 
                $simples[$key] = $value;

        $configs = array_merge($configs, parse_configs($simples));
        
        if($asObject){
            $scope = new xScope;
        
            foreach ($configs as $key => $value) 
                $scope->{$key} = $value;
        
            return $scope;
        } 

        return $configs;
    }

    /**
        Parse a composable configs array.
    */
    static function parse_configs( Array $configs){
        $unknowns = 0;
        # We will continue making replacements as long as we need to.
        do {
            # update heystack.
            $heystack = json_encode($configs);
            # will match any string wrapped in "{{...}}", with or without whitespace.
            preg_match_all('/{{\s?\w*\s?}}/', $heystack, $tokens );
            # preps...
                // get token for this pass.
                $token = array_shift($tokens[0]);
                // remove whitespace and wrapping {{ }} to reveil our key.
                $key = str_replace('{{', '', str_replace('}}', '', str_replace(' ', '', $token)));
            # keep track of unknowns, and apply to $unknows offset.
            if( !array_key_exists($key, $configs)) $unknowns++;
            # if the $key exists within the $configs array, and is it's own dependency, fail
            if( array_key_exists($key, $configs) && strstr($configs[$key], $token)) throw new Exception("Error Processing Configs, a circular reference has been detected! While processing the $token token, issue located in $configs[$key]. Self referencing is not permitted.", 1);
            # if $token has key in array, let's perform the replacments
            if( $token && array_key_exists($key, $configs)) $heystack = str_replace($token, $configs[$key], $heystack );
            # update $configs...
            $configs = json_decode($heystack, true );
        } while( count($tokens[0]) - $unknowns > 0 );
        return $configs;
    }

}
