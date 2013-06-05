<?php 
require_once 'shelf_ColumnFixture.php';

/**
 * Copyright (c) 2010-2011 MetaClass Groningen Nederland
 * Licensed under the GNU Lesser General Public License version 3 or later.
 * and GNU General Public License version 3 or later.
 */
class ColumnSetUp extends shelf_ColumnFixture {
	
	protected $systemUnderTest;
	protected $targetClass;

    public function exception($cell, $e) {
    	global $site;
    	$this->error($cell, $e->getMessage());
    	$site->errorHandler->handleException($e);
    }

    public function setSystemUnderTest($param) {
		$this->systemUnderTest = $param;
		forEach ($this->columnBindings as $adapter) 
			if ($adapter)
				$adapter->target = $param;
		reset($this->columnBindings);
	}
	
	public function getSystemUnderTest() {
		return $this->systemUnderTest;
	}
	
	function getTargetClass() {
		return $this->targetClass;
    }
	
    function setTargetClass($value) {
    	$this->targetClass = $value;
    }
 
    /**
    * Process a table's row
    *
    * @param PHPFIT_Parse $row
    */
    public function doRow($row) {
    	$class = $this->getTargetClass();
    	$this->setSystemUnderTest(new $class());
    	
    	parent::doRow($row);
    }
    
    function execute() {
    	$adapter = PHPFIT_TypeAdapter::on($this, 'validateAndSave', $this->getSystemUnderTest(), 'method');
    	return $adapter->validateAndSave($this->columnBindings);
    }
   
}
?>