<?php
namespace Kanedo;
class User {
	private $userName;
	private $firstName;
	private $lastName;
	private $dateJoined;

	public function __construct($json = NULL){
		if($json != NULL){
			$this->setUp($json);
		}
	}

	public static function byId($id){
		$json = NULL;
		return new User($json);
	}

	public function setUp($json){
		$parsed = json_decode($json);
		if($parsed != NULL){
			$this->setUserName($parsed->username);
			$this->setFirstName($parsed->first_name);
			$this->setLastName($parsed->last_name);
			$this->setDateJoined($parsed->date_joined);

		}else{
			throw new \Exception("misformated json");
			return;
		}
	}

	public function setUserName($user){
		if($user == NULL){
			throw new \Exception("no value");
		}
		$this->userName = $user;
	}

	public function getUsername(){
		return $this->userName;
	}

	public function setFirstName($name){
		if($name == NULL){
			throw new \Exception("no value");
		}
		$this->firstName = $name;
	}

	public function getFirstName(){
		return $this->firstName;
	}

	public function setLastName($name){
		if($name == NULL){
			throw new \Exception("no value");
		}
		$this->lastName = $name;
	}

	public function getLastName(){
		return $this->lastName;
	}

	public function setDateJoined($date){
		if($date == NULL){
			throw new \Exception("no value");
		}
		$this->dateJoined = $date;
	}

	public function getDateJoined($format){
		switch ($format) {
			case 'time':
				return strtotime($this->dateJoined);			
			default:
				return $this->dateJoined;
		}
	}

	public function isLoggedIn(){
		return false;
	}
}