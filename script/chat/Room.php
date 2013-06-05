<?php 
/* @package chat
 * @author Rick Mugridge 21/04/2004
 * Copyright (c) 2004 Rick Mugridge, University of Auckland, NZ
 * Released under the terms of the GNU General Public License version 2 or later.
 * Ported to PHP by MetaClass, www.metaclass.nl
 */

class Room {
	/** @var string $name */
	private $name;
	/** @var User $owner */
	private $owner;
	/** @var ChatRoom $chat */
	private $chat;
	/** @var array $users (java: Set)*/
	private $users = array();

	/** 
	* @param String $roomName
	* @param User $owner
	* @param ChatRoom $chat*/
	public function __construct($roomName, User $owner, ChatRoom $chat) {
		$this->name = $roomName;
		$this->owner = $owner;
		$this->chat = $chat;
	}
	
	/** @return Iterator */
	public function users() {
		return new ArrayIterator($this->users);
	}
	
	/** @return null
	* @param User $user */
	public function add(User $user) {
		$key = array_search($user, $this->users);
		if ($key===false) $this->users[]=$user;
	}
	
	/** @return boolean
	* @param User $user */
	public function remove(User $user) {
		$key = array_search($user, $this->users);
		if ($key!==false) { 
			unSet($this->users[$key]);
			return true;
		}
		return false;
	}
	
	/** @return boolean */
	public function getName() {
		return $this->name;
	}
	
	/** @return int */
	public function occupantCount() {
		return count($this->users);
	}
	
	/** @return array (java: Set) */
	public function &usersIn() {
		return $this->users;
	}
	
	/** @return boolean */
	public function isOpen() {
		return true;
	}
	
	/** @return null 
	* @param string $name */
	public function rename($name) {
		$this->chat->renameRoom($this,$name);
		$this->name = $name;
	}
	
	/*** @return String */
	public function getOwner() {
		return $this->owner->getName();
	}
	
	/** Workaround by MetaClass for porting to PHP:  
	 * In Java the method getOwner results in a property 'owner'. 
	 * In PHP it does not work that way, @see DoFixture::doAction
	 * This method will make the standard test work anyway
	 */
	public function owner() {
		return $this->getOwner(); 
	}
	
	/** Added by MetaClass for porting to PHP:  
	 * In Java the method getOwner results in a property 'owner'. 
	 * In PHP it does not work that way, @see DoFixture::doAction
	 * This method will make a property 'owner' available, however,
	 * according to PHP's funcion property_exists it will not exist. It can
	 * be accessed through @see DoFixture::get
	 */
	function __get($name) {
		if ($name=='owner') return getOwner();
		trigger_error("access to non-exitent member variable: '$name'", E_USER_NOTICE);
		return null;
	}
	
}
