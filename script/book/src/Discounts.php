<?php 
//import fitlibrary.ArrayFixture;
require_once 'ArrayFixture.php';
//import fitlibrary.DoFixture;
require_once 'DoFixture.php';
//import fit.Fixture;
require_once 'SubsetFixture.php';
//import fitlibrary.SubsetFixture;
require_once 'DiscountApplication.php';
require_once 'SetUpDiscounts.php';
require_once 'CalculateDiscounts2.php';
/*
 * @author Rick Mugridge 2/10/2004
 * Copyright (c) 2004 Rick Mugridge, University of Auckland, NZ
 * Released under the terms of the GNU General Public License version 2 or later.
 * Ported to PHP by MetaClass, www.metaclass.nl
 */
class Discounts extends DoFixture { //COPY:ALL
	/** @var DiscountApplication $app */
	private $app;

	function __construct() {
		$this->app = new DiscountApplication(); //COPY:ALL
	}
	 //COPY:ALL
	/** @return Fixture */
	public function setUps() { //COPY:ALL
		return new SetUpDiscounts($this->app); //COPY:ALL
	} //COPY:ALL
	/** @return Fixture */
	public function orderedList() { //COPY:ALL
		return new ArrayFixture($this->app.getGroups()); //COPY:ALL
	} //COPY:ALL
	/** @return Fixture */
	public function subset() { //COPY:ALL
		return new SubsetFixture($this->app.getGroups()); //COPY:ALL
	} //COPY:ALL
	/** @return Fixture */
	public function calculateWithFutureValue(String $futureValue) { //COPY:ALL
		return new CalculateDiscounts2($this->app,$futureValue); //COPY:ALL
	} //COPY:ALL
}
?>