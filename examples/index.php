<?php
    include 'composable_configs.php';

    $configs = mount_configs('config.php');
    $configs->echo('app_version');
    echo $configs->app_path;

    $configs = load_configs('config.php');
    $configs['run']('app_version');
    echo $configs['app_path'];