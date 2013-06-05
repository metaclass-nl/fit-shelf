<?php 
require_once 'shelf_RowFixture.php';

/**
 * Copyright (c) 2010-2011 MetaClass Groningen Nederland
 * Licensed under the GNU Lesser General Public License version 3 or later.
 * and GNU General Public License version 3 or later.
 * 
 * Inherited limitation: ::eSort and ::cSort use parsed values as array keys, will crash if they are objects
 */
class SetFixture extends shelf_RowFixture {

	protected $systemUnderTest;
	protected $targetClass;

	/** 
	 * @param Traversable $sut System Under Test
	 */
	function __construct($sut=null) {
		parent::__construct();
		if ($sut)
			$this->setSystemUnderTest($sut);
	}
	
	public function setSystemUnderTest($param) {
		$this->systemUnderTest = $param;
		if (!$this->targetClass) { //derive target class
			$current = current($param);
			if ($current !== false) $this->setTargetClass(get_class($current)); 
		}
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
    
    function query() {
    	return $this->getSystemUnderTest();
    }
    
}
?>