<?php 
/**
 * Implementation Copyright (c) 2010-2012 H. Verhoeven Beheer BV, holding of MetaClass Groningen Nederland
 * Licensed under the General Public License version 3 or later.
 */
class shelf_ClassHelper extends PHPFIT_ClassHelper {

	/** @see PHPFIT_TypeAdapter_PhpTolerant in shelf folder
	 * @param string the type to be set, or null for getting
	 * @return the type used to create a PHPFIT_TypeAdapter,
	 * 	or the previouw type if setting */ 
	static function adapterType($value=null) {
		static $adapterType;
		$result = $adapterType;
		if ($value) $adapterType = $value;
		return $result || $value ? $result : 'PhpTolerant';
	}
	
	static function getArgTypesForMethod($classOrObject, $name) {
		//based on PHPFIT_ClassHelper::getArgTypesForMethod
		//PHPFIT_ClassHelper::getArgTypesForMethod checks method to exist, blocking support for __call,
		//furthermore, modifying the same array inside forEach is problematic..
		$type = self::getTypeDictEntry($classOrObject, $name);
        if (!is_array($type)) return null;

	    $argTypes = self::getArrayValue($type, 'args');
		if (is_null($argTypes)) return null;
		 
	    $result = array();
	    foreach ($argTypes as $key => $argType) 
			$result[$key] = self::getNormalizedType($argType);
	    return $result;
	}
	
	static function getTypeOrNull($classOrObject, $name) {
	    $type = self::getTypeDictEntry($classOrObject, $name);
        if (is_array($type)) {
			$type = self::getArrayValue($type, 'return');
        }
    	return $type;
	} 
    	
}
?>