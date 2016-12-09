<?php
/**
    # Composable Configuration Files.

    # Examples:
    
    ## Simple ( Primatives )

        //... Your Configs file, using composable configurations.

            return [
                'app_root' => __DIR__,
                'app_name' => 'My App',
                'app_version' => '0.0.1',
                'app_loader' => '{{ app_root }}/{{ app_name }}/{{ app_version }}/index.php'
            ];

        //... Bootstraping your configs

        $configs = load_configs( __DIR__ . '/configs.php');

        // $configs ******************************************

            [
                'app_root' => __DIR__,
                'app_name' => 'my_app',
                'app_version' => '0.0.1',
                'app_loader' => '/path/to/app/root/my_app/0.0.1/index.php'
            ]

        // ***************************************************

    ## Advanced ( Closures, Object, nested arrays )

        NOTE: under the hood we use json_encode/json_decode to perform key 
              aspects of the parsing. As such all constructs should be JSON safe.

              We do not recursivly walk complete object/array structures, they are 
              left unaltered.


    
        //... Your Configs file, using composable configurations.

            return [
                'app_root' => __DIR__,
                'app_name' => 'My App',
                'app_version' => '0.0.1',
                'app_loader' => '{{ app_root }}/{{ app_name }}/{{ app_version }}/index.php',
                "echo" => function($key) {
                    echo $this->{$key};
                }
            ];

        //... Bootstraping your configs

        $configs = load_configs( __DIR__ . '/configs.php'); // implicit preserve as array.

        // $configs ******************************************

            [
                'app_root' => __DIR__,
                'app_name' => 'my_app',
                'app_version' => '0.0.1',
                'app_loader' => '/path/to/app/root/my_app/0.0.1/index.php'
                "echo" => object(Closure)
            ]

            USAGE: when preserved as an array.

            $configs['echo']('app_loader') // -> "/path/to/app/root/my_app/0.0.1/index.php"

        // ***************************************************

    $configs, is an array containing key => value pairs. Where the values may contain
    inline placeholders/variables, strings wrapped in "{{ ... }}". Basicly we will loop through 
    the $configs array, extracting a collection of tokens, and iterativly perform a replacement
    with the value returned by looking up the tokens' key within the $configs array.

    If there are unknown placeholders/variables, $token keys that don't exist within the $configs
    array, we will leave them intact, updating everything else.

    Order should not be an issue as all replacments are done globally accross the entire set of
    configs, as such the replacments are not done recursivly, once a token has been replaced, 
    it's been replaced everywhere.

    To simplify the code, we employ json decode/encode to transform the array to a string, 
    replace all the matches, then convert it back to an array. Turns out json_encode/json_decode 
    are super fast, faster then parsing them ourselves using loops and nested scopes.


*/


/**
    PHP will not allow us to simply call Closures that are assigned to props,
    we must employ the __call magic method to accomplish what we want.
*/
 class Scope {
    // public function __call($method, $args) {
    //     if($this->{$method}){
    //         return $this->{$method}->__invoke($this, $args);
    //     } else {
    //         return $this->{$method};
    //     }
    // }

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


/**
    Simple helper to make including/requiring config files 
    fluent. load_configs will return and array.
*/
function load_configs( $path, $preserve = true ) {
    $tmp = require $path;
    return $preserve ? parse_and_preserve_refs($tmp, false) : parse_configs($tmp);
}


/**
    Simple helper to make including/requiring config files 
    fluent, mount_configs will return an object
*/
function mount_configs( $path ) {
    $tmp = require $path;
    return parse_and_preserve_refs($tmp);
}


/**
    Helper to preserve and enhance references and closures.
*/
function parse_and_preserve_refs( $configs, $asObject = true){
    # collect all strings to parse
    $simples = []; 
    # collect all closure to bind once completed.
    $closures = [];
    
    foreach ($configs as $key => $value) 
        if(!is_callable($value) && !is_object($value) && !is_array($value)) 
            $simples[$key] = $value;

    $configs = array_merge($configs, parse_configs($simples));
    
    if($asObject){
        $scope = new Scope;
    
        foreach ($configs as $key => $value) 
            $scope->{$key} = $value;
    
        return $scope;
    } 

    return $configs;
}

/**
    Parse a composable configs array.
*/
function parse_configs( Array $configs){
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