<?php 
require_once 'PHPFIT_TypeAdapter_BeanTolerant.php';

/** Copyright (c) 2010-2011 MetaClass Groningen Nederland
 * Licensed under the GNU Lesser General Public License version 3 or later.
 * and GNU General Public License version 3 or later.
 * 
 * Tolerant adapter for PhpPeanuts.
 * The types used for fields, method results and parameters must be 
 * compatibele with StringConverter from phpPeanuts and the front controller
 * of phpPeanuts must be in global variable $site. */
class PHPFIT_TypeAdapter_PntTolerant extends PHPFIT_TypeAdapter_BeanTolerant {
	
    /** @return boolean wheather the object has a property. AccessPaths are supported.
     * Requires the properties in the path to be described by PropertyDescriptor.
     * @param object $object whose property 
     * @param $any return true if the object supports properties */
    function hasProperty($any=false) {
 		if (!method_exists($this->target, 'getPropertyDescriptor')) 
 			return parent::hasProperty();
 			
		if ($any) return true;
		
		try {
			$nav = $this->getNavigation();
		} catch (PntReflectionError $e) {
			return false;
		}
		return true;
	}
	
    function getPropertyValue() {
		$nav = $this->getNavigation();
		return $nav->evaluate($this->target);
       }
	
    function setPropertyValue($value) {
    	$nav = $this->getNavigation();
    	$nav->setValue($this->target, $value);
     }
	
     function getNavigation() {
		if (!isSet($this->navigation)) {
     		$class = $this->getTargetClass();
    		if (!$class) throw new Exception('Target has no class: '. Gen::toString($this->target));

    		$this->navigation = PntObjectNavigation::getInstance($this->field, $class);
		} 	
	     return $this->navigation;
     }
     
    /** FIT_TypeAdapter_Mixed may be used even if type is known. 
     * @return the actual type or null if unknown
     */
    function getActualType() {
    	global $site;
    	if ($this->field && $this->hasProperty()) {
		   	$nav = $this->getNavigation();
		   	$prop = $nav->getLastProp();
		   	if (!$prop) throw new Exception('Target has no last prop: '. Gen::toString($nav));
			$this->stringConverter = $site->getConverter();
			$this->stringConverter->initFromProp($prop);
 			return $nav->getResultType();
		} else {
			return parent::getActualType();
		}
    }
    
    /** @return parsed value 
     * @param string text to be parsed
     * @param string type
     * @throws Exception */
    function parseTyped($text, $type) {
    	global $site;
    	if ($text == 'null') return null;
    	
    	if (isSet($this->stringConverter)) { 
    		$converter = $this->stringConverter;
    		if ($converter->type != $type) 
    			trigger_error("Cached converter type '$converter->type' is different from '$type'", E_USER_WARNING);
    	} else {
    		$converter = $site->getConverter();
			$converter->type = $type;
    	}
		$parsed = $converter->fromLabel($text);
		if ($converter->error)
			throw new Exception($converter->error);
			
		return $parsed;
    }
    
    function validateAndSave($columnBindings=array()) {
		if (!pntIs_a($this->target, 'PntDbObject'))
			throw new Exception('Target is no PntDbObject');
		
		$clsDes = $this->target->getClassDescriptor();
		forEach($clsDes->getUiPropertyDescriptors() as $prop) 
			if (!$prop->getReadOnly()) {
				$err = $this->target->validateGetErrorString($prop, $prop->getValueFor($this->target));
				if ($err) throw new Exception($prop->getName(). ": $err");
			}
		
		$saveErrors = $this->target->getSaveErrorMessages();
		if ($saveErrors) {
			//Gen::show($this->target);
			//Gen::show($this->target->get('whole'));
			throw new Exception($saveErrors[0]);
		}

		$this->target->save();
		forEach($this->getOtherObjectsToSave($columnBindings) as $otherObj)
			$otherObj->save();
		return true;
	}
	
	function checkAndDelete() {
		if (!pntIs_a($this->target, 'PntDbObject'))
			throw new Exception('Target is no PntDbObject');
		
		$errors = $this->target->getDeleteErrorMessages();
		if ($errors) {
			//Gen::show($this->target);
			//Gen::show($this->target->get('whole'));
			throw new Exception($errors[0]);
		}
		$this->target->delete();
	}
    
	function getOtherObjectsToSave($columnBindings) {
		$result = array();
		reset($columnBindings);
		forEach($columnBindings as $columnAdapter) {
			if ($columnAdapter) {
				$nav = $columnAdapter->getNavigation();
				$setOn = $nav->getItemToSetOn($this->target);
				if (!$this->equals($this->target, $setOn))
					$result[$setOn->getOid()]=$setOn;
			}
		}
		return $result;
	}
	
	
}
?>