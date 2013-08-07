<?php
require_once 'SetFixture.php';

/**
 * Implementation Copyright (c) 2010-2012 H. Verhoeven Beheer BV, holding of MetaClass Groningen Nederland
 * Licensed under the General Public License version 3 or later.
 * 
 * Inherited limitation: ::eSort and ::cSort use parsed values as array keys, will crash if they are objects
 */
class SubsetFixture extends SetFixture {
	
	
   /** Do not build surplus rows
   * @param array $rows
   * @return PHPFIT_Parse
   */
    protected function buildRows($rows) {
	    //only used for surplus. 
    	return null;
	}
}

?>