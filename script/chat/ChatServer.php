<?php

/* @package chat
 * @author Rick Mugridge 26/12/2003
 *
 * Copyright (c) 2003 Rick Mugridge, University of Auckland, NZ
 * Released under the terms of the GNU General Public License version 2 or later.
 * Ported to PHP by MetaClass, www.metaclass.nl
 */

class ChatServer extends ChatRoom {
	
	public function __construct() {
		$this->connectUser("anna");
		$this->userCreatesRoom("anna", "lotr");
		$this->userEntersRoom("anna","lotr");
		$this->connectUser("luke");
		$this->userEntersRoom("luke","lotr");
	}
}
?>