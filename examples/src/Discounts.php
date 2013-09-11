<?php 
//import fitlibrary.ArrayFixture;
use fitshelf\ArrayFixture;
//import fitlibrary.DoFixture;
use fitshelf\DoFixture;
//import fit.Fixture;
use fitshelf\SubsetFixture;
//import fitlibrary.SubsetFixture;

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
	/** @param string $futureValue
	 * @return Fixture */
	public function calculateWithFutureValue($futureValue) { //COPY:ALL
		return new CalculateDiscounts2($this->app,$futureValue); //COPY:ALL
	} //COPY:ALL
}
?>