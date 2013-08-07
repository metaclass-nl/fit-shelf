<?php /*
 * @author Rick Mugridge 24/12/2003
 *
 * Copyright (c) 2003 Rick Mugridge, University of Auckland, NZ
 * Released under the terms of the GNU General Public License version 2 or later.
 * Ported to PHP by MetaClass, www.metaclass.nl
*/
class DiscountForGroup {
		/** @var double $maxOwing;
		 * @var double $minPurchase */
		public $maxOwing, $minPurchase;
		/** @var String $futureValue, 
		 * @var String $description */
		public $futureValue, $description;
		/** @var double $discountPercent */
		public $discountPercent;
	
		/** 
		 * @param String $futureValue
		 * @param double $maxOwing
		 * @param double $minPurchase
		 * @param double $discountPercent
		 */
		public function __construct($futureValue, $maxOwing, 
					 $minPurchase, $discountPercent) {
			$this->futureValue = $futureValue;
			$this->maxOwing = $maxOwing;
			$this->minPurchase = $minPurchase;
			$this->discountPercent = $discountPercent;
			$this->description = "";
		}
		/**
		 * @return double
		 * @param String $futureValue
		 * @param double $owing
		 * @param double $purchase
		 */
		public function discount(String $futureValue, double $owing, double $purchase) {
			if ($this->futureValue == $futureValue && $owing <= $this->maxOwing && $purchase >= $this->minPurchase)
				return $purchase * $this->discountPercent/100;
			return 0.0;
		}
}
?>