<?php 
namespace fitshelf;

class ParamRowFixture extends SetFixture {
	
	function __construct($sut, $targetClass=null) {
		$this->setTargetClass($targetClass);
		parent::__construct($sut);
	}
}
?>