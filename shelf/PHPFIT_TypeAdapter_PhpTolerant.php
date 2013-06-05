<?php
/**
 * Copyright (c) 2010-2011 MetaClass Groningen Nederland
 * Licensed under the GNU Lesser General Public License version 3 or later.
 * and GNU General Public License version 3 or later.
 * 
 * PHPFIT has been ported from Java, a strong typed language with a single 
 * metamodel for the System Under Test (Beans). PHP is weakly typed, has automatic 
 * type conversion between primitive types and different frameworks have 
 * their own metamodel for the System Under Test. This is a little too much 
 * for the design of PHPFIT's TypeAdapters. Shelf intends to redesign the
 * Typeadapters along three dimensions: 
 * 1. The metamodel for the System Under Test. 
 * 2. The conversion according to the typing of fields, method results and method parameters.
 * 3. The aproach to coping with missing type information on fields, method results and method parameters
 * The first and third dimension combined decides the adapter type returned by the 
 * ::getType method of all fixtures using this kind of adapters. Currently available are:
 * PhpTolerant, BeanTolerant and PntTolerant.
 * The second dimension is resolved by delegation to another TypeAdapter or 
 * in some other way specific to the framework it adapts to.
 * 
 * This (PhpTolerant) implements a tolerant aproach to normal php style objects, that 
 * use fields instead of properties and may use the magic methods __get, __set and __call,
 * and whose type information, comparision and parsing methods may be missing.
 * For historic reasons the handling of mixed, array and object typed 
 * fields, method results and method parameters is included in this class instead of
 * delegated to other type adapters.
 * 2DO: Factor out PHPFIT_TypeAdapter_Strict, 
 * PHPFIT_TypeAdapter_Array, PHPFIT_TypeAdapter_Object (should try (super)class-specific ones first) 
 * PHPFIT_TypeAdapter_PhpTolerant (for conversion and comparision of mixed type).
 * 		tries to cope with mixed type at runtime.
 *
 * HACK: The name of this class really has start with PHPFIT_TypeAdapert_ , 
 * but is not part of PHPFIT. It has to be included expicitly by the fixture that requires it.
 */ 
class PHPFIT_TypeAdapter_PhpTolerant extends PHPFIT_TypeAdapter {

	public $precision = 0.000001;  //like PHPFIT_TypeAdapter_Double
	
	/** Static method used for parsing if type is a class and method exists */
	public $staticParseMethod = 'parse';
   
	/**
	 * This method will try to compare specific to a non-string type
	 * of one of the values.
	 * If one of the values is an object and it has method 'equals' that 
	 * method is used. 
	 * If both are objects or arrays and the output of print_r contains *RECURSION*
	 * the outputs of print_r are compared to prevent Fatal error:  Nesting level too deep.
	 * This is a compromise as the order of keys matters and 
	 * print_r prints 0 different from false or null.
	 * If both are numeric and at least one is float, they are compared with PRECISION
	 * In all other cases comparision is done through == 
	 * @param mixed $a if called by Action::check, reference (the result of parsing the string from the test)
	 * @param mixed $b if called by Action::check, actual (the value from the method)
	 * @result boolean wheather the supplied values seem to be equal
	 */
	public function equals($a, $b) {
    	if (is_object($a) && method_exists($a, 'equals'))
    		return $a->equals($b);
	    if (is_object($b) && method_exists($b, 'equals') )
	    	return $b->equals($a);

	    if (is_object($a) && is_object($b)
	    		|| is_array($a) && is_array($b)) {
       		//prevent PHP Fatal error:  Nesting level too deep	
	    	$aPrintR = print_r($a, true);
	    	$bPrintR = print_r($b, true);
	    	
	     	if (strPos($aPrintR, '*RECURSION*') !== false ||
	    			strPos($bPrintR, '*RECURSION*') !== false )
	    		return $aPrintR == $bPrintR;
	    }
	    if (is_object($b) || is_array($b)) {
	    	return $this->valueToString($a) == $b;
	    }
	    if (is_numeric($a) && is_numeric($b) 
		    	&& (is_float($a) || is_float($b)) )
	    	return abs($b - $a) < $this->precision;
	    
	    return $a == $b;
    }

    /** Overridden to pass $value to ::parse
     * @param mixed $value
     * @param string $text
     * @return true if same, false otherwise
     */
    public function valueEquals($value, $text) {
    	$type = null;
    	//$type = $this->getValueType($value); // type may be wrong due to loose typing or a bug we want to test for
        return $this->equals($this->parse($text, $type), $value);
    }
    
    /** @return the type of the value
     */
    function getValueType($value) {
    	if ($value === null) return null;
   		if (is_object($value)) return get_class($value);
    	return getType($value);
    }
    
    /** @return parsed value or unparsed string if unable to parse
     * @param string text to be parsed
     * @param string type or null if unknown
     * @throws Exception if type supplied is not supported or a parse error occurs */
    public function parse($text, $type=null) {
    	if (!$type) 
    		$type = $this->getActualType();
    	if ($type) 
    		return $this->parseTyped($text, $type);
    	
    	//rely on automatic conversion of PHP
    	if ($text == 'null') return null;
   		return $text; //will be converted by php when necesary
    }
    
    public function valueToString($value) {
        if (is_null($value)) 
        	return 'null';
        if (is_object($value)) {
        	if (method_exists($value, '__toString'))
        		return $value->__toString();
        	if (method_exists($value, 'toString'))
            	return $value->toString();
            return print_r($value, true);
        }
        if (is_array($value))
			return print_r($value, true);
			
        return strval($value);
	}
    
	/** Overridden to factor out getFieldValue
	 * @return mixed
	 */
	public function get() {
		if ($this->field != null) 
			return $this->getFieldValue();
	
		if ($this->method != null) 
			return $this->invoke();
	
	 return null;
	}
	
	function getFieldValue() {
		return $this->getMemberValue($this->target);
	}
	
    /** factored out from PHPFIT_TypeAdapter::get
     * @precodition $this->field contains the name of the field
     * @param object $object whose field
     * @return mixed the value of a field */
    function getMemberValue($object) {
		$field = $this->field;
		return $object->$field;	
    }

    /** FIT_TypeAdapter_Mixed may be used even if type is known. 
     * @return the actual type or null if unknown
     */
    function getActualType() {
    	$adaptorClass =  "PHPFIT_TypeAdapter_$this->type";
    	if (!($this instanceof $adaptorClass))
    		return $this->type;

    	$this->type = null;
    	if (method_exists($this->target, 'getActualType'))
	    	$this->type = $this->fixture->getActualType($this->target, $this->getName(), $this->getProperty());
    	if ($this->type) return $this->type;
    	
    	return $this->type = shelf_ClassHelper::getTypeOrNull($this->target, $this->getName());
    }
    
    /** @return parsed value 
     * @param string text to be parsed
     * @param string type
     * @throws Exception */
    function parseTyped($text, $type) {
    	//if to compare with an object, try to create instance through static method ::parse
   		if (class_exists($type) && method_exists($type, $this->staticParseMethod))
    		return call_user_func(array($type, $this->staticParseMethod), $text);
    	
    	$adapter = $this->getAdapterForType($type);
    	return $adapter->parse($text);
    }
    
    /** @return PHPFIT_TypeAdapter for the supplied type
     * @param string type
     * @throws Exception */
    function getAdapterForType($type) {
    	$adapter = PHPFIT_TypeAdapter::adapterFor($type);
    	$adapter->init($this->fixture, $type);
    	$adapter->target = $this->target;
    	$property = $this->getProperty();
		$adapter->$property = $this->getName();
        return $adapter;
    }
    
	function getName() {
		$prop = $this->getProperty();
		return isSet($this->$prop) ? $this->$prop : null;
    }
    
    /** must be compatible with ::get 
     * @return string the property that was passed to ::on */
    function getProperty() {
    	if (isSet($this->arrayAt)) return 'arrayAt';
    	if ($this->field) return 'field';
    	if ($this->method) return 'method';
    }
    
    function hasMethod() {
    	return method_exists($this->target, $this->method);
    }
    
    function getTargetClass() {
    	if (is_array($this->target)) return 'Array';
    	return is_object($this->target) ? get_class($this->target) : $this->target;
    }
    
    function validateAndSave() {
    	//$this->target->validate();
    	$this->invoke();
    	return true;
    }
    
    function checkAndDelete() {
    	//$this->target->checkDeletion();
    	$this->invoke();
    	return true;
    }
    
	function __toString() {
		return get_class($this)
			. '('
			. $this->getName()
			. ')';
	}     
}

?>