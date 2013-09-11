<?php
namespace fitshelf;

require_once('PHPFIT/Fixture.php'); //needs to be in include_path set in php.ini
require_once 'PHPFIT/TypeAdapter.php'; //needs to be in include_path set in php.ini
//spl_autoload_call('PHPFIT_TypeAdapter_PhpTolerant');
//require_once('PHPFIT_TypeAdapter_PhpTolerant.php');  //must be included explicitly

/**
 * Implementation Copyright (c) 2010-2012 H. Verhoeven Beheer BV, holding of MetaClass Groningen Nederland
 * Licensed under the GNU General Public License version 3 or later.
 * 
 * Lightweight implementation of fitlibrary DoFixture
 *
 * shelf Extra's:
 * - Tolerant Type adapters for metamodel of System Under Test 
 * 	 also handling mixed type 
 * - 'deny' command behaves like 'reject' but does not deny exceptions (not tested)
 * - 'error' command checks for specific exceptions specified in last cell (not tested)
 * - array access (not tested)
 * - initially does action from args and set systemUnderTest to result
 */
class DoFixture extends \PHPFIT_Fixture {

	protected $systemUnderTest;
	public $argsProcessed=false;
	/** Many systems have actions that return null if sucessfull. If you set $sutActionsNullIsRight
	 * to true, null results of method calls on the system under test will be annotated 'right'.  */
	public $sutActionsNullIsRight=false; 
	public $defaultDoFixture = 'shelf.DoFixture';

	function __construct($sut=null) {
		parent::__construct();
		$this->setSystemUnderTest($sut);
	}
	
	public function setSystemUnderTest($param) {
		$this->systemUnderTest = $param;
	}
	
	public function getSystemUnderTest() {
		return $this->systemUnderTest;
	}
	
	//execution from PHPFIT_Fixture starts here
	protected function interpretTables(&$tables) {
	    $tables = $tables->more;
	    $this->interpretTablesInit();
	    while ($tables != null) {
	        try {
	        	// Don't create the first fixture again, because creation may do something important.
	        	// Don't collect args
	            // $this->getArgsForTable($tables);
	             
	            $this->doTable($tables);
	        } catch (\Exception $ex) {
	            $this->exception($tables->at(0, 0, 0), $ex);
	        }
	        $this->listener->tableFinished($tables);
	        if ($tables != null) {
	            $tables = $tables->more;
	        }
	    }
	    $this->listener->tablesFinished($this->counts);
	    $this->tearDown();
	    self::clearSymbols();
	}

	protected function interpretTablesInit() {
		//ClassHelper::adapterType('PhpTolerant'); not necessary, is default
	}
	
    public function doTable($table) {
        $this->doRows($table->parts);
    }
    
    protected function tearDown() {
    	//default is to do nothing
    }
	
    /**
    * iterate through rows as long as doRow returs true
    * execution from DoFixture starts here
    *
    * @param PHPFIT_Parser $rows
    * @see doRow()
    */
    public function doRows($rows) {
    	if (!$this->argsProcessed)
    		$this->processArgs();
    	$result=null;
        while (!$result && $rows != null) {
            $result = $this->doRow($rows);
            $rows = $rows->more;
        }
        if ($result) 
        	$result->doRows($rows); //maybe try catch...
    }

    /** do action from args and set systemUnderTest to the result 
	* please override to process args an other way */
    function processArgs() {
    	$this->argsProcessed = true;
    	if (empty($this->args)) return;
    	
	    $doCells = new DoCells($this, null);
	    $doCells->doArgs($this->args);
	    $this->setSystemUnderTest($doCells->getResult());
    }
    
    //overridden to return fixture that should do rest of rows
	public function doRow($row) {
		return $this->doCells($row->parts);
	}
    
	/** @return boolean wheater to continue with next row */
	public function doCells($cells) {
        $this->cells = $cells;
        $txt1 = $cells->text();
        if ($txt1=='check') return $this->doCheck($cells);
        if ($txt1=='reject' || $txt1=='not') return $this->doReject($cells);
        if ($txt1=='deny') return $this->doReject($cells, false);
        if ($txt1=='error') return $this->doError($cells);
        if ($txt1=='note') return $this->doNote($cells);
        if ($txt1=='show') return $this->doShow($cells);
            
        try {
            $doCells = $this->doRestOfCells($cells, false);
        } catch (\Exception $e) {
	        $this->exception($cells, $e);
	        return;
	    }
	     return $this->doneCells($doCells);
	}
	
	function doneCells($doCells) {
        $result = $doCells->getResult();
//print "\n".get_class($this).": "; print_r($result);        

        if (is_bool($result)) {
            $this->annotateAll($result ? 'right' : 'wrong', $doCells->getActionCells());
            return;
        }
        if ($result instanceof \PHPFIT_Fixture) {
            $result->counts = $this->counts;
        	$result->summary = $this->summary;
            return $result;
		}
        if (is_array($result) || $result instanceOf Iterator) 
            return $this->arrayFixtureFor($result, $doCells);
        if ($this->isCollection($result)) 
            return $this->arrayFixtureFor($result->getIterator(), $doCells);
        if (is_object($result)) 
            return $this->doFixtureFor($result, $doCells);
    
       if ($this->sutActionsNullIsRight && $result === null 
       	&& $doCells->getProperty() == 'method' && $doCells->getObject() !== $this) 
			 $this->annotateAll('right', $doCells->getActionCells());
 	}
	
	function doCheck($cells) {
		try {
    		$doCells = $this->doRestOfCells($cells->more, true);
    	} catch (\Exception $e) {
	        $this->exception($cells->more, $e);
	        return;
	    }
        $adapter = $doCells->getAdapter();
//print_r($adapter);
        try {
        	$text = $doCells->getLastCell()->text();
            if ($adapter->valueEquals($doCells->getResult(), $text)) {
                $this->right($doCells->getLastCell());
            } else {
                $this->wrong($doCells->getLastCell(), $adapter->valueToString($doCells->getResult()));
            }
        } catch(\Exception $e) {
            $this->exception($doCells->getLastCell(), $e);
        }
	}
	
	/** Run the action in the rest of the cells ignoring the last (like doCheck)
	 * Check the last cell against the exception thrown. Annotate as right if:
	 * - it holds the message of the exception
	 * - it holds the class of the exception
	 * - it holds <class>(<message>)
	 * Otherwise annotate the last cell as wrong, expecting <class>(<message>)
	 * If no exception is thrown, annotate as wrong.
	 */
	function doError($cells) {
		$doCells = new DoCells($this, $cells->more);
		try {
			$doCells->evaluate(true);
    	} catch (\Exception $e) {
    		$text = $doCells->getLastCell()->text();
    		$exceptionClass = get_class($e);
    		$message = $e->getMessage();
    		if ($message == $text || $exceptionClass == $text
    				|| "$exceptionClass($message)" == $text)
    			 $this->right($doCells->getLastCell());
    		else 
    			 $this->wrong($doCells->getLastCell(), "$exceptionClass($message)");
    		return;
    	}
    	$this->wrong($cells);
	}
	
	/** Run the action in the rest of the cells. 
	 * If the action would have been colered green, the reject cell is colored red.
	 * If the action would have been colored red, the reject cell is colored green.
	 * @param PHPFIT_Parse $cells 
	 * @param boolean $rejectExceptions wheather to color the recect cell green on exceptions.
	 * 		If false, exceptions are shown the usual way.
	 * 		True for 'reject' and 'not', false for 'deny'.
	 */
	function doReject($cells, $rejectExceptions=true) {
		try {
			$doCells = $this->doRestOfCells($cells->more, false);
			if ($doCells->getResult()===true) 
				$this->wrong($cells);
		    elseif ($doCells->getResult()===false)
		    	$this->right($cells);
		    else
			    $this->unexpectedResult($cells, $doCells->getResult()); 
    	} catch (\Exception $e) {
    		if ($rejectExceptions)
	    		$this->annotateAll('right', $doCells->getActionCells());
	    	else
	        	$this->exception($cells->more, $e);
	    }
	}
	    
	function doShow($cells) {
		try {
			$doCells = $this->doRestOfCells($cells->more, false);
    	} catch (\Exception $e) {
	        $this->exception($cells->more, $e);
	        return;
	    }
        $adapter = $doCells->getAdapter();
	    $newCell = \PHPFIT_Parse::createSimple('td', $adapter->valueToString($doCells->getResult()) );
	    $doCells->getLastCell()->more = $newCell;
	}
	    
	function doNote($cells) {
	    $this->ignore($cells);
	}
		
	function doRestOfCells($cells, $ignoreLast) {
	    $doCells = new DoCells($this, $cells);
	    $doCells->evaluate($ignoreLast);
	    return $doCells;
	}

	/** Collections must support getIterator to return an instance of Iterator */
	function isCollection($value) {
		return $value instanceOf ArrayObject;
	}
	
	function arrayFixtureFor($traversable, $doCells) {
		$fixture = $this->loadFixture('shelf.ArrayFixture');
		//$fixture = $this->loadFixture('shelf.SubsetFixture');
		$adapter = $doCells->getAdapter();
		if ($adapter)
			$fixture->setTargetClass($adapter->getActualType());
		return $this->initFixture($fixture, $traversable); //may pass parameters too?
	}
	
	function doFixtureFor($obj, $doCells) {
		$fixture = $this->loadFixture($this->defaultDoFixture);
		return $this->initFixture($fixture, $obj); //may pass parameters too?
	}
	
	/** override to load shelf. fixtures */
//	function loadFixture($javaStylishName) {
// 		if (subStr($javaStylishName, 0, 6) != 'shelf.') {
// 			return parent::loadFixture($javaStylishName);
//      }
// 		$this->loadFile($javaStylishName);
// 		$className = subStr($javaStylishName, 6);
// 		return new $className();
// 	}
	
	function initFixture($fixture, $obj, $parameters=null) {
        $fixture->counts = $this->counts;
        $fixture->summary = $this->summary;
        if (method_exists($fixture, 'setSystemUnderTest'))
			$fixture->setSystemUnderTest($obj);
		$fixture->args = array(); 
        while ($parameters != null) {
            $fixture->args[] = $parameters->text();
            $parameters = $parameters->more;
        }
        return $fixture;
	}
	
	function loadFile($javaStylishName) {
		$filename = str_replace('.', '/', $javaStylishName) . '.php';
		$dir = rtrim($this->fixturesDirectory, '/\\') . '/';
		if (PHPFIT_Fixture::fc_incpath('is_readable', $dir.$filename))
			return require_once($dir.$filename);
			
		$this->checkFixtureName(subStr($javaStylishName, 6));
		return require_once(subStr($filename, 6));
	}
	
	static function checkFixtureName($javaStylishName) {
		if (strLen($javaStylishName)==0) return; 
		if ($javaStylishName[0]=='.' || preg_match("'[^A-Za-z0-9_\-.]'", $javaStylishName))
			throw new \Exception("unsafe fixture name : '$javaStylishName'");
	}
	
	protected function unexpectedResult($cell, $result) {
        $type = getType($result);
        if ($type=='object') $type=get_class($type);
        $this->error($cell, "Unexpected result type $type:". print_r($result, true) );
	}
	
	function annotateAll($annotation, $cells) {
    	forEach($cells as $cell)
    		$this->$annotation($cell);
    }
    
    function parameter($cell) {
    	$cell->addToTag(self::getCssProperty('parameter'));
    }
    
    function action($cell) {
    	$cell->addToTag(self::getCssProperty('action'));
    }
    
    static function getCssProperty($type) {
    	if ($type=='parameter')
    		return sprintf(' style="%s"', 'font-weight: bold');
    	if ($type=='action')
    		return sprintf(' style="%s"', 'font-style: italic');
       	return parent::getCssProperty($type);
    }

    /** @see PHPFIT_TypeAdapter_PhpTolerant in shelf folder
     * @return string type of PHPFIT_TypeAdapter
     */
	static function getType($classOrObject, $name, $property) {
		return ClassHelper::adapterType();
	}
	
	/** fitlibrary.DoFixture will if it exists: 
	 * 1. call method on the flow fixture object (=$this)
	 * 2. call method on systemUnderTest,
	 * 3. get property on the flow fixture object, 
	 * 4. get property on systemUnderTest*,
	 * 5. instantiate a fixture class
	 * In PHP, the existence of methods and properties is tested through
	 * method_ exists resp. property_exists, but because of magic 
	 * methods __call, __get and __set this will leave some behavior 
	 * that can not be tested. 
	 * To to call non-existent methods and get or set non-existent properties 
	 * the following methods are defined on shelf.DoFixture:
	 * - call
	 * - get 
	 * - set
	 * These methods will be called under 1 (see above). 
	 * fitshelf Extra's:
	 * - array access
	 * - specific property access (to be implemented by subclass) 
	 * @param DoCells $doCells holding action name and params
     * @return mixed the result
     * @throws \Exception if fixture class could not be loaded or instantiated (see 5.)
	 */
	function doAction($doCells) {
		$sut = $this->getSystemUnderTest();
		$name = $doCells->getName();
		$params = $doCells->getParams();
        $myAdapter = \PHPFIT_TypeAdapter::on($this, $name, $this, 'method');

		if ($myAdapter->hasMethod()) //##HACK should not call methods defined on DoFixture or its superclasses 
			return $this->call($doCells, $myAdapter);

        $sutAdapter = \PHPFIT_TypeAdapter::on($this, $name, $sut, 'method');
		if ($sutAdapter->hasMethod()) 
			return $this->call($doCells, $sutAdapter);

		$myAdapter->method = null;
		$myAdapter->field = $name; 
//print get_class($this). " no method: '$name' on: "; print_r($sut);
		if ($myAdapter->hasMember()) 
			return $this->get($doCells, $myAdapter);

		$sutAdapter->method = null;
		if (is_array($sut)) { 
			$sutAdapter->arrayAt = $name;
			$doCells->setAdapter($sutAdapter);
			return $sut[$name]; //?2DO: delegate to adapter
		}
		$sutAdapter->field = $name;
		if ($sutAdapter->hasProperty() || $sutAdapter->hasMember()) 
			return $this->get($doCells, $sutAdapter);

		//$doCells->setProperty('fixture');
		return $this->loadFixture($name);
	}

	/** In PHP, the existence of methods and properties is verified throug
	 * method_ exists resp. property_exists, but because of magic 
	 * methods __call, __get and __set this will leave some behavior 
	 * that can not be tested. 
	 * To get non-existent properties this method will allways
	 * get a property on the systemUnderTest. 
	 * @param DoCells $doCells holding action name and params
     * @return mixed the property value
     */
	function get($doCells, $adapter=null) {
		if (!$adapter) 
			$adapter = \PHPFIT_TypeAdapter::on($this, $doCells->getName(), $this->getSystemUnderTest(), 'field');
		$doCells->setAdapter($adapter);
		return $adapter->get();
	}
	
	/** To set non-existent properties this method will allways
	 * set a property on the systemUnderTest. 
	 * @param DoCells $doCells holding action name and params
     */
	function set($doCells, $adapter=null) {
		if (!$adapter) 
			$adapter = \PHPFIT_TypeAdapter::on($this, $doCells->getName(), $this->getSystemUnderTest(), 'field');
		$doCells->setAdapter($adapter);
		$params = $doCells->getParams();
		$adapter->set($adapter->parse($params[0]));
	}
	
	/** Call a method with parameters
	 * @param DoCells $doCells holding action name and params
	 * @param PHPFIT_TypeAdapter $adapter 
	 * 		If no adapter is supplied, this method will create one on the  systemUnderTest. 
	 * 		This allows tests call a non-existent method. Risk is that a fatal error may occur. 
     * @return mixed the result of the method call
	 */
	function call($doCells, $adapter=null) {
		if (!$adapter) 
			$adapter = \PHPFIT_TypeAdapter::on($this, $doCells->getName(), $this->getSystemUnderTest(), 'method');
		$doCells->setAdapter($adapter); //result-adapter
		$arguments = $this->parseArgumentsForMethod($adapter->target, $doCells->getName(), $doCells->getParams(), $adapter);
		return call_user_func_array(array($adapter->target, $doCells->getName()), $arguments);
	}
	
	/** As far as no argument types, params (strings) are passed */
	function parseArgumentsForMethod($classOrObject, $name, $params, $mixedAdapter) {
		$types = (array) $this->getArgTypesForMethod($classOrObject, $name, $params);
		$arguments = $params;
		forEach($types as $i => $type) {
			if ($type) {
				$arguments[$i] = $mixedAdapter->parseTyped($params[$i], $type); //may return string if it can not parse
				if (class_exists($type) && !is_object($arguments[$i]) ) 
					$arguments[$i] = new $type($params[$i]);
			}
		}
		return $arguments;
	}
	
	function getArgTypesForMethod($classOrObject, $name, $params) {
		return ClassHelper::getArgTypesForMethod($classOrObject, $name);
	} 
	
	function columnSetUp($targetClassName) {
		$fixture = $this->loadFixture('fitshelf.ColumnSetUp');
		$fixture->setTargetClass($targetClassName);
		return $fixture;
	}
}
?>