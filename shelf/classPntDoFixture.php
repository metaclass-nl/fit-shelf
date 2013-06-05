<?php 
Gen::includeClass('PntObjectNavigation', 'pnt/meta');
require_once 'DoFixture.php';
require_once('PHPFIT_TypeAdapter_PntTolerant.php'); //in shelf folder

/* 
 * Copyright (c) 2010-2011 MetaClass Groningen Nederland
 * Licensed under the GNU Lesser General Public License version 3 or later.
 * and GNU General Public License version 3 or later.
 * 
 * DoFixture Specialized for phpPeanuts.
 */
class PntDoFixture extends DoFixture {

	public $sutActionsNullIsRight=true; 
	public $defaultDoFixture = 'shelf.PntDoFixture';
	
	// functions for use by fixtures, protected if possible -----------------------------
	
    public function exception($cell, $e) {
    	global $site;
    	$this->error($cell, $e->getMessage());
//    	$site->errorHandler->handleException($e);
    }
    
	protected function interpretTablesInit() {
		parent::interpretTablesInit();
		shelf_ClassHelper::adapterType('PntTolerant');
	}
	
	/** allow class loading through Gen::tryIncludeClass */
	public function loadFixture($javaStylishName) {
		$this->checkFixtureName($javaStylishName);
		$pieces = explode('.', $javaStylishName);
		$className = $pieces[count($pieces)-1];
		array_pop($pieces);
		$path = implode('/', $pieces);
		$included = Gen::tryIncludeClass($className, $path); //warning: tryIncludeClass params must be checked for include safety!!!
		if ($included) return new $className(); 
		
		return parent::loadFixture($javaStylishName);
	}

	protected function runQuery($sql) {
		$qh = new QueryHandler();
		$qh->query=($sql);
		$qh->_runQuery();
		if ($qh->error) throw new Exception($qh->error);
		
		if (strtolower(substr(trim($qh->query),0,11))=="insert into")
			return $qh->insertId;

		if ($qh->result!==true)
			return $qh->getAssocRows();
	}

	/** @return PntSqlSpec 
	 * @throws PntError if invalid path */
	protected function getFilter($className, $path, $value=null, $comparator='=') {
		includeClass('PntSqlFilter', 'pnt/db/query');
		
		$result = PntSqlFilter::getInstance($className, $path);
		$result->set('comparatorId', $comparator);
		$result->set('value1', $value);
		
		return $result;
	}
	
	function getArgTypesForMethod($classOrObject, $name, $params) {
		if ($name == 'retrieveAWithEquals') {
			$filter = $this->getFilter($params[0], $params[1]);
			return array('string', 'string', $filter->getValueType());
		}
		return parent::getArgTypesForMethod($classOrObject, $name, $params);
	}

	// public functions, for use in tests ------------------------------------------------------
	
	function beginTransaction() {
		$qh = new QueryHandler();
		$qh->beginTransaction(); //not going to commit so all will be rolled back
	}
	
	function commitTransaction() {
		$qh = new QueryHandler();
		$qh->commit(); 
	}
	
	function rollbackTransaction() {
		$qh = new QueryHandler();
		$qh->rollback(); 
	}
	
	function useConverter($stringConverterClass) {
		global $site;
		$site->converter = new $stringConverterClass();
	}
	
	function createNew($className) {
		$this->checkFixtureName($className);
		return new $className();
	}
	
	function validateAndSave() {
    	$adapter = PHPFIT_TypeAdapter::on($this, 'save', $this->getSystemUnderTest(), 'method');
    	return $adapter->validateAndSave();
	}
	
	function checkAndDelete() {
    	$adapter = PHPFIT_TypeAdapter::on($this, 'delete', $this->getSystemUnderTest(), 'method');
    	return $adapter->checkAndDelete();
	}

	function fromAtKey($path, $index) {
		$sut = $this->getSystemUnderTest();
		$nav = PntObjectNavigation::getInstance($path, get_class($sut));
		$collected = $nav->collectAll(array($sut));
		if (isSet($collected[$index])) return $collected[$index];
		return false;
	}
	
	/** Do navigational query. Sorts by labelSort defined by the specified class.
	 * @param $className type to search for
	 * @param string $path to search by
	 * @param mixed $value to search by (dynamically typed by meta data)
	 * @return first object found, or null if none */
	function retrieveAWithEquals($className, $path, $value) {
		if (!class_exists($className)) throw new Exception('class does not exist: '. $className);
		$clsDes = PntClassDescriptor::getInstance($className);
		
		$filter = $this->getFilter($className, $path, $value);
		$sort = $clsDes->getLabelSort();
		$sort->setFilter($filter);
		
		$qh = $clsDes->getSelectQueryHandler();
		$qh->addSqlFromSpec($sort);
		$qh->query .= ' LIMIT 1';
		
		$found = $clsDes->getPeanutsRunQueryHandler($qh);
		if (!$found) return false;
		return $found[0];
	}
	
	function makeClone() {
		$sut = $this->getSystemUnderTest();
		$result = clone $sut;
		$result->pntOriginal = $sut;
		return $result;
	}
	
	function finischCopy() {
		$sut = $this->getSystemUnderTest();
		$sut->copyFrom($sut->pntOriginal);
		return $sut;
	}
	
}
?>