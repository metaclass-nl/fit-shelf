<?php 

//import java.util.ArrayList;
//import java.util.Iterator;
//import java.util.List;
require_once 'DiscountForGroup.php';
/*
 * @author Rick Mugridge 24/12/2003
 *
 * Copyright (c) 2003 Rick Mugridge, University of Auckland, NZ
 * Released under the terms of the GNU General Public License version 2 or later.
 * Ported to PHP by MetaClass, www.metaclass.nl
 */
class DiscountApplication {
	/** @var List $groups */
	private $groups = array();

	/** 
	 * @param String $futureValue
	 * @param double $maxOwing
	 * @param double $minPurchase
	 * @param double $discountPercent
	 */
	public function addDiscountGroup($futureValue, $maxOwing, $minPurchase, $discountPercent) {
		$this->groups[] = new DiscountForGroup($futureValue,$maxOwing,$minPurchase,$discountPercent);
	}
	/** @return array */
	public function getGroups() {
		return $this->groups;
	}
	/**
	 * @return double
	 * @param String $futureValue
	 * @param double $owing
	 * @param double $purchase
	 */
	public function discount($futureValue, $owing, $purchase) {
		for ($it = new ArrayIterator($this->groups); $it.hasNext(); ) {
			$group = $it.next();
			$discount = $group->discount($futureValue,$owing,$purchase);
			if ($discount > 0)
				return $discount;
		}
		return 0;
	}
}
?>