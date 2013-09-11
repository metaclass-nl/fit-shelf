<?php 
namespace fitshelf;

/**
 * Implementation Copyright (c) 2010-2012 H. Verhoeven Beheer BV, holding of MetaClass Groningen Nederland
 * Licensed under the GNU General Public License version 3 or later.
 */
class ArrayFixture extends SetFixture {

	/** @var int Maxmum depth when searching for a matching object
	 * This is a compromise: with a large max. depth many objects searched through 
	 * may be annotated as surplus when a match is made in the end, 
	 * with a small max. depth the row searched for may be annotated too easily as
	 * missing because the small max. depth has been reached. 
	 */
	public $maxObjectSearchDepth = 3;
	/** @var int Maxmum depth when searching for a matching row */
	public $maxRowSearchDepth = 3;
	protected $previousRow;
	
	public function doRows($rows) {
		$this->previousRow = $rows; //should be the header row
		parent::doRows($rows);
	}
	
	
    /**
     * arrays may be associative 
     *
     * @param array of PHPFIT_Parse $expected 
     * @param array of targetClass $computed 
     * @param integer $col colunm index (ignoored)
     */
    protected function match($expected, $computed, $col) {
    	$this->searchCount = 1; //only used for debugging 
    	while ($expected && $computed) {
    		$this->searchAndRemove($expected, $computed);
    		$this->searchCount++;
    	}
   		$this->missing = array_merge($this->missing, $expected);
   		$this->surplus = array_merge($this->surplus, $computed);
    }
    
    /** Find first matching object for first row and first matching row for first object.
     *	Use the one that required the shortest search.
     *	If no match, annotate first row as missing.
     *	If a match is made, annotate non-matching until match as surplus/missing,
     *	annotate check-results for match,
     *	and remove both non-matching and matching from the search.
     *
     * @param array of PHPFIT_Parse $expected rows 
     * @param array of targetClass $computed objects
     */
    protected function searchAndRemove(&$expected, &$computed) {
    	$this->currentRowKey = key($expected);
    	$objectCount = $this->searchObjects(current($expected), $computed);
    	//store search results
    	$oEndRowKey = $this->currentRowKey;
    	$oEndObjectKey = $this->currentObjectKey;
    	reset($computed);
    	
    	$this->currentObjectKey = key($computed);
    	$rowCount = $this->searchRows(current($computed), $expected);
    	reset($expected);
    	
    	//compare search results and remove found
    	if ($objectCount && (!$rowCount || $objectCount <= $rowCount) ) { //use object found
//print "<br>use object found at '$oEndObjectKey' ";
//print " with row at '$oEndRowKey'";
			$row = $expected[$oEndRowKey];
			forEach(array_keys($computed) as $key) {
//print "<br>checking key $key";
    			if ($key != $oEndObjectKey) { //insert surplus-row in table and remove in-beween (surplus) object from $computed
//print "<br>remove in-betwen object at: $key and insert surplus row before '$oEndRowKey'";
					$newRow = $this->buildRows(array($computed[$key]));
		            $this->markArray(array($newRow), 'surplus');
					$nextRow = $this->previousRow->more;
		            $this->previousRow->more = $newRow;
					$newRow->more = $nextRow;
					unSet($computed[$key]);
    				$this->previousRow = $nextRow;
    			} else {
    				return $this->processMatch($expected, $computed, $oEndRowKey, $oEndObjectKey);
    			}
    		}
    	} elseIf ($rowCount && (!$objectCount || $objectCount > $rowCount)) {//use row found
//print "<br>use row found at '$oEndObjectKey' ";
//print " with object at '$this->currentObjectKey' ";
    		forEach(array_keys($expected) as $key) {
				$row = $expected[$key];
    			if ($key != $this->currentRowKey) { //remove in-between (missing) row from $expected 
//print "<br>remove in-betwen row at: $key and mark it missing";
    				$this->missing[] = $row;
    				unSet($expected[$key]);
    				$this->previousRow = $row;
    			} else {
    				return $this->processMatch($expected, $computed, $this->currentRowKey, $this->currentObjectKey);
    			}
    		}
        } else {//nothing found, remove (missing) row from $expected
//print "<br>nothing found, remove missing row at '$oEndRowKey'";
 			$this->previousRow = current($expected);
        	$this->missing[] = current($expected);
        	unSet($expected[key($expected)]);
        }
    }

    protected function processMatch(&$expected, &$computed, $rowKey, $objectKey) {
		$annotatedClone = $this->checked[$rowKey][$objectKey];
		$expected[$rowKey]->parts = $annotatedClone;
    	$this->previousRow = $expected[$rowKey];
		unSet($expected[$rowKey]);
		unSet($computed[$objectKey]);
		
	}
    	    
    /** Search through the objects to $this->maxObjectSearchDepth
     * @return int count until match or false if no match was made */
    protected function searchObjects($needle, $hayStack) {
    	$count = 1;
    	$bestMatch = 0;
    	forEach($hayStack as $this->currentObjectKey => $each) {
    		$count++;
    		$match = $this->checkRow($needle, $each);
    		if ($match > $bestMatch) {
    			$bestMatch = $match;
    			$matchKey = $this->currentObjectKey;
    			$matchCount = $count;
    		}
    		if ($count >= $this->maxObjectSearchDepth) break;
    	}
    	if ($bestMatch) {
    		$this->currentObjectKey = $matchKey;
    		return $matchCount;
    	}
    	return 0;
    }
    
    /** Search through the rows to a $this->maxRowSearchDepth
     * @return int count until match or false if no match was made */
    protected function searchRows($needle, $hayStack) {
    	$count = 1;
    	$bestMatch = 0;
    	forEach($hayStack as $this->currentRowKey => $each) {
    		$count++;
    		$match = $this->checkRow($each, $needle);
    		if ($match > $bestMatch) {
    			$bestMatch = $match;
    			$matchKey = $this->currentRowKey;
    			$matchCount = $count;
    		}
        	if ($count >= $this->maxRowSearchDepth) break;
    	}
   		if ($bestMatch) {
    		$this->currentRowKey = $matchKey;
    		return $matchCount;
   		}
    	return 0;
    }

    /** Check a row against an object. Because :checkCell does also annotate
     *	we need to clone the rows before chekcing and store the clones for later use.
     * Afterwards $this->checked[rowKey][objectKey] contains annotated clone of row cells,
     * $this->matches[rowKey][objectKey] the match result
     * and $this->right[rowKey][objectKey] the cells that where checked to be right. 
     * 
     * @param PHPFIT_Parse $parse to get expected values from
     * @param object $obj to get actual values from
     * @return int number of matching columns or 0 if no match
     */
    protected function checkRow($parse, $obj) {
    	if (!isSet($this->matches[$this->currentRowKey][$this->currentObjectKey])) {
	    	$cell = clone $parse->parts;
	    	$this->checked[$this->currentRowKey][$this->currentObjectKey] = $cell;
	    	$this->currentColumnIndex = 0;
	    	$columnsUsed = 0;
	    	while ($cell) {
	    		//if ($cell->count != $this->currentColumnIndex) die('Different count - error in cloning? May cause endless loop');
	    		$adapter = $this->columnBindings[$this->currentColumnIndex];
	            if ($adapter != null && strLen($cell->text()) != 0) {
	            	$adapter->target = $obj;
		            $this->checkCell($cell, $adapter);
		            $columnsUsed++;
				} //else ignore 
	            if ( $cell->more)
		            $cell->more = clone $cell->more;
	            $cell = $cell->more;
	            $this->currentColumnIndex++;
	        }
	        $this->matches[$this->currentRowKey][$this->currentObjectKey] = $this->doesCurrentMatch($columnsUsed);
	    } //else a match was previously stored. 
        return  $this->matches[$this->currentRowKey][$this->currentObjectKey];
    }
    
    protected function doesCurrentMatch($columnsUsed) {
        $matchCount = isSet($this->right[$this->currentRowKey][$this->currentObjectKey])
        	? count($this->right[$this->currentRowKey][$this->currentObjectKey])
        	: 0;
//print "\n<br>$columnsUsed $matchCount";
        if ($columnsUsed < 3) 
        	return ($columnsUsed = $matchCount) ? $matchCount : 0; //all required
        if ($columnsUsed < 5) { 
        	return ($columnsUsed - $matchCount < 2) ? $matchCount : 0; //all but one required
        }
        return ($matchCount * 5 >= $columnsUsed * 4) ? $matchCount : 0; //80 % required
    }
    
    /** Register $cell as a right.
     * For now we annotate it too (messes up statistics ;-( )
     * @param PHPFIT_Parse $cell
     */
    public function right($cell) {
    	$this->right[$this->currentRowKey][$this->currentObjectKey][$this->currentColumnIndex] = $cell;
    	$this->basicRight($cell);
    }
    
    /**
     * Add annotation to cell: right
     *
     * @param PHPFIT_Parse $cell
     */
    public function basicRight($cell) {
    	parent::right($cell);
    }
    
    public function exception($cell, $e) {
//    	global $site;
    	$this->error($cell, $e->getMessage());
//Gen::show($e);
//    	$site->errorHandler->handleException($e);
    }
}
?>