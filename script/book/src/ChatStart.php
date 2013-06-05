<?php
require_once('DoFixture.php'); //includes fit.Fixture
require_once('chat/ChatRoom.php');
require_once('chat/User.php');
require_once('chat/Room.php');
//NYI in shelf: fitlibrary.ParamRowFixture;
//class loading moved to constuctor to use Java stylishc names 
//and load relative to fixtures dir

/*
 * @author Rick Mugridge 22/05/2004
 * Copyright (c) 2004 Rick Mugridge, University of Auckland, NZ
 * Released under the terms of the GNU General Public License version 2 or later.
 * Ported to PHP by MetaClass, www.metaclass.nl
 */

class ChatStart extends DoFixture {
	
	/** @var ChatRoom $chat */
	private $chat;
	
	function __construct() {
		parent::__construct();
		
		$this->chat=new ChatRoom();
		$this->setSystemUnderTest($this->chat);
	}
	
	/** @return boolean
	 * @param String $userName  */
	public function connectUser($userName) { //COPY:ONE
		return $this->chat->connectUser($userName); //COPY:ONE
	} //COPY:ONE
	
	/** @return boolean
	 * @param String $userName 
	 * @param String $roomName */
	 public function userCreatesRoom($userName, $roomName) { //COPY:ONE
		return $this->chat->userCreatesRoom($userName,$roomName); //COPY:ONE
	} //COPY:ONE
	
	/** @return boolean
	 * @param String $userName 
	 * @param String $roomName */
	public function userEntersRoom($userName, $roomName) { //COPY:ONE
		return $this->chat->userEntersRoom($userName,$roomName); //COPY:ONE
	} //COPY:ONE
	
	/** @return Fixture
	 * @param String $roomName */
	public function usersInRoom($roomName) { //COPY:ONE
		//added by MetaClass
		throw new Exception('Not yet implemented: ParamRowFixture, UserCopy');

		$users = $this->chat->usersInRoom($roomName); //COPY:ONE
		$collection = array(); //COPY:ONE
		reset($users);
		forEach($users as $user) { //COPY:ONE
			$collection[] = new UserCopy($user->getName()); //COPY:ONE
		} //COPY:ONE
		return new ParamRowFixture($collection,'UserCopy'); //COPY:ONE
	} //COPY:ONE
	
	/** @return boolean
	 * @param String $userName  */
	public function disconnectUser($userName) { //COPY:ONE
		return $this->chat->disconnectUser($userName); //COPY:ONE
	} //COPY:ONE
	
	/** @return int
	 * @param String $roomName  */
	public function occupantCount($roomName) { //COPY:ALL
		return $this->chat->occupants($roomName); //COPY:ALL
	} //COPY:ALL
	/*
	public SetFixture usersInRoom(String roomName) {//COPY:TWO
		return new SetFixture(chat.usersInRoom(roomName));//COPY:TWO
	}//COPY:TWO
	*/
	
	/** @return Fixture
	 * @param String $roomName  */
	public function usersInRoom2($roomName) {
		return new ParamRowFixture($this->chat->usersInRoom($roomName),'User');
	}
	
	/** @return UserFixture
	 * @param String $userName  */
	public function connect($userName) { //COPY:THREE //COPY:FOUR
		$this->loadFixture('UserFixture'); //added by MetaClass for porting to PHP
		if ($this->chat->connectUser($userName)) //COPY:THREE //COPY:FOUR
			return new UserFixture($this->chat, $this->chat->user($userName)); //COPY:THREE //COPY:FOUR
		throw new Exception("Duplicate user"); //COPY:THREE //COPY:FOUR
	} //COPY:THREE //COPY:FOUR
	
	/** @return DoFixture
	 * @param String $roomName  */
	public function room($roomName) { //COPY:THREE
		return new DoFixture($this->chat->room($roomName)); //COPY:THREE
	} //COPY:THREE
	
	/** @return boolean
	 * @param String $roomName  */
	public function roomIsEmpty($roomName) {
		return $this->chat->occupants($roomName) == 0;
	}
}
?>