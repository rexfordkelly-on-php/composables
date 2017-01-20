<?php namespace Rexfordge\x;

class xCheckpoint {

    /**
		Comming Soon - xCheckpoints

		The idea with xCheckpoints is to provide a caching mechanizm
		for blocks of code or entire php scripts or processes.

		One use case could be site configs or constants that don't usually change
		between requests, or where you have data that can be reduced down to a
		serielizable object or array. 

		Let's suppose you dynamicly build up a tree of data, like for a site menu
		or perhaps an array of important paths of your site or application. While 
		building up this data may be computationally expensive or time consuming, or 
		perhaps involves multiple includes or excesive accessing of the filesystem.

		You could instead employ a checkpoint to seriralize and store the results for 
		reuse later, then control if/when such data should be rebuilt and cached for 
		later.
    */

}