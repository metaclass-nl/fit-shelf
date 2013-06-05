<?php

/* 
 * Copyright (c) 2010-2011 MetaClass Groningen Nederland
 * Licensed under the GNU Lesser General Public License version 3 or later.
 * and GNU General Public License version 3 or later.
 * 
 * Object of this class take care of reading and processing a single
 * action from the cells till (one before) the end (of the row)
 * keeping track of cell roles and the result.
 * naming of methods getName, getObject and getProperty
 * is based on the names of parameters of PHPFIT_TypeAdapter::on
 */ 
class DoCells {
	
    protected $actionCells = array();
    protected $paramCells = array();
    protected $action = '';
    protected $params = array();
    protected $result;
	
	function __construct($fixture, $cells) {
		$this->fixture = $fixture;
		$this->cells = $cells;
	}
	
	function evaluate($ignoreLast) {
		$txt1 = $this->cells->text();
		if ($txt1=='get' || $txt1=='set' || $txt1=='call') {
			$this->initFromCells($this->cells->more, $ignoreLast);
			$this->result = call_user_func(array($this->fixture, $txt1), $this);
		} else {
			$this->initFromCells($this->cells, $ignoreLast);
        	$this->result = $this->fixture->doAction($this);
		}
    }

    function doArgs($args) {
    	$this->initFromArgs($args);
    	$this->result = $this->fixture->doAction($this);
    }
    
    function initFromArgs($args) {
    	for ($i=0; $i<count($args); $i+=2) {
			if ($this->action) $this->action .= ' ';
			$this->action .= $args[$i];
			if (isSet($args[$i+1])) 
				$this->params[]=$args[$i+1];
    	}
    }
    
    //allows more paramters after empty action cell. Empty action cells are ignored 
    function initFromCells($current, $ignoreLast) {
    	while ($current && (!$ignoreLast || $current->more)) {
    		if ($current->text()) {
	    		$this->actionCells[] = $current;
	    		if ($this->action) $this->action .= ' ';
	    		$this->action .= $current->text();
    		}
    		if (isSet($current->more) && (!$ignoreLast || isSet($current->more->more)) ) {
    		 	$current = $current->more;
    		 	$this->paramCells[]=$current;
    		 	$this->params[] = $current->text();
    		}
    		$current = isSet($current->more) ? $current->more : null;
    	}

    	$this->fixture->annotateAll('parameter', $this->getParamCells());
    	$this->fixture->annotateAll('action', $this->getActionCells());
    	
//print "\n<br>action: '$this->action'<pre>";
//print_r($this->params);
    }
    
    function getActionCells() {
    	return $this->actionCells;
    }
    
    function getParamCells() {
    	return $this->paramCells;
    }
    
    function getAction() {
    	return trim($this->action);
    }
    
    function getParams() {
    	return $this->params;
    }
    
    function getLastCell() {
    	return $this->cells->last();
    }
    
    function getName() {
    	return $this->fixture->camel($this->getAction());
    }
    
    function getResult() {
    	return $this->result;
    }
    
    /** @param PHPFIT_TypeAdapter */
    function setAdapter($value) {
    	$this->adapter = $value;
    }
    
    
    /** @return PHPFIT_TypeAdapter */
    function getAdapter() {
    	return $this->adapter;
    }
    
    /** @return object (may be a fixture) the target of the adapter */
    function getObject() {
    	$adapter = $this->getAdapter(); 
    	if ($adapter) return $adapter->target;
    }
    
    /** @return string the property that was passed to PHPFIT_TypeAdapter::on */
    function getProperty() {
    	$adapter = $this->getAdapter();
    	if (!$adapter) return null;
    	return $adapter->getProperty();
    }
    
}
?>