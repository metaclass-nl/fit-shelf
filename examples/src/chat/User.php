<?php 

namespace chat;

/* @package chat
 * @author Rick Mugridge 18/04/2004
 * Copyright (c) 2004 Rick Mugridge, University of Auckland, NZ
 * Released under the terms of the GNU General Public License version 2 or later.
 * Ported to PHP by MetaClass, www.metaclass.nl
 */

class User {
	/** @var string $name */
	public $name;

	/** @param string $name */
	function __construct($name) {
		$this->name = $name;
	}
	
	/** @return string */
	public function getName() {
		return $this->name;
	}
	
}
?>