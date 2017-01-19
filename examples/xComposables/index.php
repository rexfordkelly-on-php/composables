<?php require __DIR__ . '../vendor/autoload.php';

	use Rexfordge\x\xComposable as Rx;

	// Mount Array, transform into xContext object.
	$Rx::mount('config.php')->echo('app_version');

	// Txt files
	echo Rx::mount('messages.txt')->last();
