<?php require __DIR__ . '../vendor/autoload.php';

	use Rexfordge\x\xComposable as Rx;

	$c = Rx::mount('config.php');
	$c->echo('app_version');
