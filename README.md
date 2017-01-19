    #######################################################################################
    ###                                                                                 ###
    ###     RexfordKelly (x) Packages                                                   ###
    ###     An experiment in making PHP a little more fluent.                           ###
    ###                                                                                 ###
    #######################################################################################

        - xContext
        - xURLFromPath
        - xComposable

    #######################################################################################
    ###                 xContext - Objects with callable properties                     ###
    #######################################################################################

        Description and Examples comming soon.




    #######################################################################################
    ###      xURLFromPath - Make working with navigatable paths simpler                 ###
    #######################################################################################

        Description and Examples comming soon.




    #######################################################################################
    ###                   xComposable - Composable Configuration Files.                 ###
    #######################################################################################

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
                'echo' => function($scope, $key){
                    echo $scope->{$key};
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

            USAGE: when trnasformed into Object.

            $configs->echo('app_loader') // -> "/path/to/app/root/my_app/0.0.1/index.php"

        // ***************************************************

    ## Json files

        {
            "app_name": "{{ app_url }}/{{ app_version }}",
            "app_version": "0.0.1",
            "app_url": "http://www.google.com"
        }

        USAGE: is the same as above.

    ## txt files

        //---
            John Doe
            Welcome {{ 0 }} to the new world.
            {{1}} You should be see this!
        //---

        As there are no keys just line numbers with text files, substitutions are based on line numbers.
        Of course it's a zero based index.

            $c = X::mount('messages.txt');
            echo $c->{count(get_object_vars($c)) -1}; // -> "Welcome John Doe to the new world. You should be see this!"

        USAGE:

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
