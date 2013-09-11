<?php 
namespace fitshelf;

require_once('PHPFIT/Fixture/Column.php');

//2DO: decide which superclass

/** 
 * Implementation Copyright (c) 2010-2012 H. Verhoeven Beheer BV, holding of MetaClass Groningen Nederland
 * Licensed under the General Public License version 3 or later.
*/
abstract class SetupFixture extends \PHPFIT_Fixture_Column {
	
    function __construct() {
        throw new \Exception('Not Yet Implemented');
    }
	//todo: bind columns to parameters - zie DoFirstCell? 
}

?>