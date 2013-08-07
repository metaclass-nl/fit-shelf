<?php
require_once 'SetUpFixture.php';

/*
 * @author Rick Mugridge 12/10/2004
 * Copyright (c) 2004 Rick Mugridge, University of Auckland, NZ
 * Released under the terms of the GNU General Public License version 2 or later.
 * Ported to PHP by MetaClass, www.metaclass.nl
 */
class SetUpDiscounts extends SetUpFixture { //COPY:ALL
	/** @var DiscountApplication $app */
	private $app; //COPY:ALL
	 //COPY:ALL
	/** @param DiscountApplication $app */
	public function __construct($app) { //COPY:ALL
		$this->app = app; //COPY:ALL
	} //COPY:ALL
	
	/** 
	 * @param String $futureValue
	 * @param double $maxBalance
	 * @param double $minPurchase
	 * @param double $discountPercent
	 */
	public function futureValueMaxBalanceMinPurchaseDiscountPercent( //COPY:ALL
			$futureValue, $maxBalance, $minPurchase, //COPY:ALL
			$discountPercent) { //COPY:ALL
		$this->app.addDiscountGroup($futureValue,$maxBalance, //COPY:ALL
                $minPurchase,$discountPercent); //COPY:ALL
	} //COPY:ALL
} //COPY:ALL
?>