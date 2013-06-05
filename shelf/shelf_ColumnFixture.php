<?php
require_once('PHPFIT/Fixture/Column.php');

/** 
 * @author Henk Verhoeven 2010-03-06
 * Copyright (c) 2010-2011 MetaClass Groningen Nederland
 * Licensed under the GNU Lesser General Public License version 3 or later.
 * and GNU General Public License version 3 or later.
*/
abstract class shelf_ColumnFixture extends PHPFIT_Fixture_Column {
	
    /** Fix for array misinterpretation by PHPFIT_Fixture_Column::bind:
     * In case of an exception no key is made for the column index,
     * causing PHPFIT_Fixture_Row::checkList to use the adapter of the next column.  */
	protected function bind($heads) {
		parent::bind($heads);
		$corrected = array(); 
		for ($i = 0; $heads != null; $heads = $heads->more) {
			$corrected[$i] = array_key_exists($i, $this->columnBindings)
				? $this->columnBindings[$i]
				: null;
			$i = $i+1;
		}
		$this->columnBindings = $corrected;
	}
	
    /** @see PHPFIT_TypeAdapter_PhpTolerant in shelf folder
     * @return string type of PHPFIT_TypeAdapter
     */
	static function getType($classOrObject, $name, $property) {
		return shelf_ClassHelper::adapterType();
	}

}

?>