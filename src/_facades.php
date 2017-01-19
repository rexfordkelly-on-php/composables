<?php

use Rexfordge\x\xURLFromPath as xURLFromPath;

// Sudo Facade for xURLFromPath
function urlTo($Absolute_Path){
	return ( new xURLFromPath( $Absolute_Path ) );
}