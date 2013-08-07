<?php 
Gen::includeClass('PntObjectNavigation', 'pnt/meta');
require_once 'DoFixture.php';
require_once('PHPFIT_TypeAdapter_PntTolerant.php'); //in shelf folder

/* 
 * Implementation Copyright (c) 2010-2012 H. Verhoeven Beheer BV, holding of MetaClass Groningen Nederland
 * Licensed under the GNU General Public License version 3 or later.
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

	public function doCells($cells) {
        $this->cells = $cells;
        $name = $this->camel($cells->text());
        if (!in_array($name, array('where','andWhere','orWhere'))) return parent::doCells($cells);

        //navigational query support, get command from first cell and parameters from the rest 
        require_once('DoFirstCell.php'); //in shelf folder
        try {
		    $doCells = new DoFirstCell($this, $cells);
		    $doCells->evaluate(false);
        } catch (Exception $e) {
	        $this->exception($cells, $e);
	        return;
	    }
	     return $this->doneCells($doCells);
	}
	
	function getArgTypesForMethod($classOrObject, $name, $params) {
		if ($name == 'retrieveAWithEquals') {
			$filter = $this->getFilter($params[0], $params[1]);
			return array('string', 'string', $filter->getValueType());
		}
		$sut = $this->getSystemUnderTest();
		if (in_array($name,array('where','andWhere','orWhere')) && Gen::is_a($sut, 'PntSqlSpec')) {
			$nav = PntNavigation::getInstance($params[0], $sut->get('itemType'));
			return array('string', 'string', $nav->getResultType());
		}
				
		return parent::getArgTypesForMethod($classOrObject, $name, $params);
	}

	protected function tearDown() {
    	$this->restoreGlobalFilters();
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
	
	function suspendGlobalFilters() {
		global $site;
		$filters = $site->getGlobalFilters();
		if (!$filters) return null;
		
		$this->supendedGlobalFilters = $filters;
		$filters = array(); //will be passed by reference
		$site->setGlobalFilters($filters);
		return true;
	}
	
	function restoreGlobalFilters() {
		global $site;
		if (!isSet($this->supendedGlobalFilters)) return null;
		
		$filters = $this->supendedGlobalFilters; //will be passed by reference
		$this->supendedGlobalFilters = null;
		$site->setGlobalFilters($filters);
		return true;
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
	
	/** (Navigational query DSL) 
	 * @return PntSqlSpec for filtering/retrieving/sorting
	 * @param sring $className >>itemType type of items to filter/retrieve/sort
	 */
	function from($className) {
		if (!class_exists($className)) throw new Exception('class does not exist: '. $className);
		$clsDes = PntClassDescriptor::getInstance($className);
		$result = new PntSqlSpec($className);
		$result->set('itemType', $className);
		return $result;
	}
	
	/** (Navigational query DSL)
	 * @return PntSqlSort for sorting the results of this 
	 * @param string $direction 'ASC' or 'DESC'
	 * @param string $path the path to sort by
	 * PRECONDITION: system under test is a PntSqlSpec
	 * @throws PntReflectionError if path does not exist from System under test>>itemType
	 */
	function sortBy($direction, $path) {
		if ($direction=='ascending') $direction = 'ASC';
		if ($direction=='descending') $direction = 'DESC';
		$sut = $this->getSystemUnderTest();
		$result = $sut->sortBy($path, $direction);
		return $result;
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