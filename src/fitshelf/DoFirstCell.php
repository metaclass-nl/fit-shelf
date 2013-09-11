<?php
namespace fitshelf;


/* 
 * Implementation Copyright (c) 2010-2012 H. Verhoeven Beheer BV, holding of MetaClass Groningen Nederland
 * Licensed under the GNU General Public License version 3 or later.
 * 
 * Object of this class take care of reading and processing a single
 * action from the first cell interpretig the rest till (one before) the end (of the row)
 * as parameters, keeping track of cell roles and the result.
 */ 
class DoFirstCell extends DoCells {
	
    function initFromCells($current, $ignoreLast) {
	    $this->actionCells[] = $current;
    	$this->action = $current->text();
    	$current = $current->more;
    	while ($current && (!$ignoreLast || $current->more)) {
   		 	$this->paramCells[]=$current;
   		 	$this->params[] = $current->text();
   		 	$current = $current->more;
   		}
 
    	$this->fixture->annotateAll('parameter', $this->getParamCells());
    	$this->fixture->annotateAll('action', $this->getActionCells());
    	
//print "\n<br>action: '$this->action'<pre>";
//print_r($this->params);
    }
	
}
?>