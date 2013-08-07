<?php
require_once('PHPFIT/Fixture/Column.php');
require_once('shelf_ClassHelper.php');
require_once('PHPFIT_TypeAdapter_PhpTolerant.php');

/** 
 * Implementation Copyright (c) 2010-2012 H. Verhoeven Beheer BV, holding of MetaClass Groningen Nederland
 * Licensed under the General Public License version 3 or later.
*/
abstract class shelf_ColumnFixture extends PHPFIT_Fixture_Column {
	
    /** @see PHPFIT_TypeAdapter_PhpTolerant in shelf folder
     * @return string type of PHPFIT_TypeAdapter
     */
	static function getType($classOrObject, $name, $property) {
		return shelf_ClassHelper::adapterType();
	}

}

?>