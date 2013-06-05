<?php 
require_once('PHPFIT_TypeAdapter_PhpTolerant.php');

/** Copyright (c) 2010-2011 MetaClass Groningen Nederland
 * Licensed under the GNU Lesser General Public License version 3 or later.
 * and GNU General Public License version 3 or later.
 * 
 * Tolerant adapter supporting beans-style properties. 
 * In Java the Beans standard describes getter and setter methods for properties 
 * starting with get and set, followed by the property name with upper case first).
 * This adapter gets and sets such properties if they exist, if not it defaults 
 * to member variables/php style properties.
 */
class PHPFIT_TypeAdapter_BeanTolerant extends PHPFIT_TypeAdapter_PhpTolerant {
	
    /** Get the value of a field. Try in the following order:
     * 1. a property on the target 
     * 2. a member on the target
     * (FitLibrary) SetFixture treats a header label as the name of a property if there 
     * is no instance variable (member). However, in (FitLibrary) DoFixture actions supercede 
     * properties, and fields are not tried at all.
     * In order to support the magic method __get on the system under test,
     * a member on the system under test is acessed after the other options have been tried. 
     * @precodition $this->field contains the name of the field
     * @return mixed the value of a field */
    function getFieldValue() {
    	return ($this->hasProperty($this->target)) 
    		? $this->getPropertyValue($this->target)
    		: $this->getMemberValue($this->target); //may result in __set being called
    }
    
    /** @return boolean wheather the object has a property. Only properties
     * with a property getter are supported. 
     * @param object $object whose property */
    function hasProperty() {
    	$getter = 'get'. ucFirst($this->field);
    	return method_exists($this->target, $getter);
    }
    
    function getPropertyValue() {
    	$getter = 'get'. ucFirst($this->field);
    	return $this->target->$getter();
    }

    /** @return boolean wheather the object has a member. Returns false if the member
     * is not explicitly present, even if a magic method __get exist.
     * @param object $object */
    function hasMember() {
    	//current implementation does not allow private properties, see ClassHelper::checkPropertyExists
		return property_exists($this->target, $this->field);
	}

    /** Sets the value of a field. @see ::getFieldValue
     * @param mixed $value
     * @precodition $this->field contains the name of the field
     */
    public function set($value) {
    	return ($this->hasProperty()) 
    		? $this->setPropertyValue($value)
    		: $this->setMemberValue($value); //may result in __set being called
    }
    
    //factored out from PHPFIT_TypeAdapter::set
    function setMemberValue($value) {
        // suggested by Julian Harris
        $field = $this->field;
        $this->target->$field = $value;
    }
    
    function setPropertyValue($value) {
    	$setter = 'set'. ucFirst($this->field);
    	return $this->target->$setter($value);
    }

}
?>