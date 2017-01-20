<?php

use Rexfordge\x\xURLFromPath as xURLFromPath;
use Rexfordge\x\xComposable as xComposable;
use Rexfordge\x\xContext as xContext;

// Sudo Facade for xURLFromPath
function urlTo($Absolute_Path){
	return ( new xURLFromPath( $Absolute_Path ) );
}

// Sudo Facade for xContext
function Context($raw){
	return ( new xContext( $raw ) );
}

// Sudo Facade for xContext
function Composable($raw){
	return ( new xComposable( $raw ) );
}