<?php 

/* @package chat;
 * @author Rick Mugridge 21/12/2003
 *
 * Copyright (c) 2003 Rick Mugridge, University of Auckland, NZ
 * Released under the terms of the GNU General Public License version 2 or later.
 * Ported to PHP by MetaClass, www.metaclass.nl
 */

class ChatRoom { //COPY:ALL
	/** @var array $users */
	private $users = array();
	/** @var array $rooms */
	private $rooms = array();
	
	/** @return boolean
	* @param string $userName */
	public function connectUser($userName) { //COPY:ALL
		// ...  //COPY:ALL
		if ($this->user($userName) != null)
			return false;
		$this->users[$userName] = new User($userName);
		return true;
	} //COPY:ALL

	/** @return boolean 
	* @param string $userName */
	public function disconnectUser($userName) { //COPY:ALL
		// ...  //COPY:ALL
		if (!isSet($this->users[$userName])) return false;
		$user = $this->users[$userName];
		unSet($this->users[$userName]);
		$it = $this->getRooms();
		forEach($it as $current)
			$current->remove($user);
		return true;
	} //COPY:ALL

	/** @return boolean 
	* @param string $userName
	* @param string $roomName */
	public function userCreatesRoom($userName, $roomName) { //COPY:ALL
		// ...  //COPY:ALL
		$user = $this->user($userName);
		if ($user == null)
			throw new Exception("Unknown user name: ".$userName);
		$this->createRoom($roomName,$user);
		return true;
	} //COPY:ALL

	/** @return null
	* @param string $roomName 
	* @param User $user */
	public function createRoom($roomName, User $user) {
		if (isSet($this->rooms[$roomName]))
			throw new Exception("Duplicate room name: ".$roomName);
		$this->rooms[$roomName] = new Room($roomName,$user,$this);
	}

	/** @return boolean 
	* @param string $userName
	* @param string $roomName */
	public function userEntersRoom($userName, $roomName) { //COPY:ALL
		// ...  //COPY:ALL
		$user = $this->user($userName);
		$room = $this->room($roomName);
		if ($user == null || $room == null)
			return false;
		$room->add($user);
		return true;
	}
	
	/** @return boolean 
	* @param string $userName
	* @param string $roomName */
	public function userLeavesRoom($userName, $roomName) {
		$user = $this->user($userName);
		$room = $this->room($roomName);
		if ($user == null || $room == null)
			return false;
		return $room->remove($user);
	}
	
	/** @return int
	* @param string $roomName */
	public function occupants($roomName) { //COPY:ALL
		// ...  //COPY:ALL
		$room = $this->room($roomName);
		if ($room == null)
			throw new Exception("Unknown room: ". $roomName);
		return $room->occupantCount();
	}
	
	/** @return boolean 
	* @param string $userName 
	* @param double $fee */
	public function userPaysDollarFee($userName, $fee) {
		return true;
	}
	
	/** @return Iterator */
	public function getRooms() {
		return new ArrayIterator($this->rooms); 
	}
	
	/** @return boolean
	* @param string $roomName */
	public function removeRoom($roomName) {
		$room = $this->room($roomName);
		if ($room == null)
			return false;
		if ($room->occupantCount() > 0)
			return false;
		unSet($this->rooms[$roomName]);
		return true;
	}
	
	/** @return User 
	* @param string $userName */
	public function user($userName) {
		return isSet($this->users[$userName])
			? $this->users[$userName] 
			: null;
	}
	
	/** @return Room
	* @param string $roomName */
	public function room($roomName) {
		return isSet($this->rooms[$roomName])
			? $this->rooms[$roomName]
			: null;
	}
	
	/** @return Iterator
	* @param string $roomName */
	public function usersIn(String $roomName) {
		$room = $this->room($roomName);
		if ($room == null)
			throw new Exception("Unknown room");
		return $room->users();	
	}
	
	/** @return array
	* @param string $roomName */
	public function usersInRoom($roomName) {
		$room = $this->room($roomName);
		if ($room == null)
			throw new Exception("Unknown room");
		return $room->usersIn();	
	}

	/** @return null
	* @param Room $room
	* @param string $ame */
	public function renameRoom(Room $room, $name) {
		unSet($this->rooms[$room->getName()]);
		$this->rooms[$name]=$room;
	}
} //COPY:ALL
?>