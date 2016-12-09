<?php

return [
	"app_version" => '0.0.1',
	"app_root" => dirname(__DIR__),
	"app_name" => 'stitch',
	"app_path" => '{{app_root}}/{{app_name}}/{{app_version}}',
	"echo" => function($scope, $key) {
		echo $scope->{$key};
	},
	"run" => function($key) {
		echo $key;
	}
];