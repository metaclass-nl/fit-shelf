<?php 
require_once('PHPFIT/Fixture/Row.php');

/** 
 * Copyright (c) 2010-2011 MetaClass Groningen Nederland
 * Licensed under the GNU Lesser General Public License version 3 or later.
 * and GNU General Public License version 3 or later.
 * 
 * Inherited limitation: ::eSort and ::cSort use parsed values as array keys, will crash if they are objects
*/
abstract class shelf_RowFixture extends PHPFIT_Fixture_Row {
	
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

   /**
    * Travel each column and check each cell
    * For non-matching values rows/objects are assumed to be missing resp. surplus
    * For unique values that match rows/objects are asumed to match
    * For non-unique values that match next column is searched 
    * Rows with parse errors are ignoored (unless already matched by a previous column)
    * Objects with errors are marked surplus (unless already matched by a previous column)
    * Limitations:
    * - ::eSort and ::cSort use parsed values as key, array will error if they are objects or arrays
    * - a single errorneous value may result in a mismatch
    *
    * @param array of PHPFIT_Parse $expected 
    * @param array of targetClass $computed 
    * @param integer $col colunm index
    * 
    * inherited from PHPFIT_Fixture_Row 
    protected function match($expected, $computed, $col) {
        if ($col >= count($this->columnBindings)) {
            $this->checkList($expected, $computed); //no more columns, check and annotate
        } elseif ($this->columnBindings[$col] == null) {
            $this->match($expected, $computed, $col+1); //no adapter, skip column
        } else {
            $eColumn = $this->eSort($expected, $col); // expected column, result contains arrays of complete rows by value
            $cColumn = $this->cSort($computed, $col); // computed column, result contains arrays of domain objects by value
            $keys = array_merge(array_keys($eColumn), array_keys($cColumn));
            $keys = array_unique($keys);
            foreach ($keys as $key) {
                $eList = $cList = null;
                if (array_key_exists($key, $eColumn))
					$eList = $eColumn[$key];
                if (array_key_exists($key, $cColumn))
					$cList = $cColumn[$key];

                if (!$eList) {
                    $this->surplus = array_merge($this->surplus, $cList);
                } elseif (!$cList) {
                    $this->missing = array_merge($this->missing, $eList);
                } else if ((count($eList) == 1) && (count($cList) == 1)) {
                    $this->checkList($eList, $cList); //unique value, check and annotate
                } else {
                    $this->match($eList, $cList, $col+1); //use next column for matching non-unique values
                }

            }
        }
    }
    */

    function showCounts($array) {
    	$counts = array();
    	forEach($array as $key => $nestedArray)
    		$counts[$key] = count($nestedArray);
    	Gen::show($counts); //like print_r
    }
	
	
}

?>