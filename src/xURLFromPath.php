<?php namespace Rexfordge\x;


// Sudo Facade for xURLFromPath
function urlTo($Absolute_Path){
	return ( new xURLFromPath( $Absolute_Path ) );
}

class xURLFromPath {

	## Properties

			   private $path = '/';
		private static $DOC_ROOT = '';
	
	## Primary Methods

		public function urlTo( $Absolute_Path )
		{
			$this->path = str_replace( _DOC_ROOT, '', $Absolute_Path);
			
			return $this;
		}
		
		public function append( $str )
		{
			$this->path = $this->path . $str;
			return $this;
		}
		
		public function prepend( $str )
		{
			$this->path = $str . $this->path;
			return $this;
		}

	## Additional Methods

		function __construct(  $Absolute_Path, $DOC_ROOT = NULL )
		{
			if( !$DOC_ROOT && ! defined( '_DOC_ROOT' ) ){
				 throw new Exception("xURLFromPath requires a defined _DOC_ROOT or one provided when invoked. No such _DOC_ROOT exists.", 1);
			} else {
				self::$DOC_ROOT = $DOC_ROOT ? $DOC_ROOT : _DOC_ROOT;
			}

			$this->urlTo($Absolute_Path);

			return $this;
		}

		public function __toString()
		{
        	return $this->path;
    	}
}