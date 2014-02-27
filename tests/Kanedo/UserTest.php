<?php
namespace Kanedo;
class UserTest extends \PHPUnit_Framework_Testcase {
	private $testee;

	public function setUp(){
		$this->testee = new User();
		$user = '{
    "username": "jdoe",
    "first_name": "John",
    "last_name": "Doe",
    "date_joined": "2010-10-08 12:00:17",
    "has_active_subscription": false,
    "reading_limit": 20,
    "email_into_address": "jdoe+crumb@inbox.readability.com",
    "kindle_email_address": "johndoe@kindle.com",
    "tags": [
        {"text": "music",    "applied_count": 1, "id": 123, "bookmark_ids": [4]},
        {"text": "New York", "applied_count": 3,  "id": 46, "bookmark_ids": [12, 45, 99]}
    ]
}';
		$this->testee->setUp($user);
	}

	/**
	 * @expectedException \Exception
	 */
	public function testMisformatedJSON(){
		$testee = new User();
		$json = "{this is wroooong: lols'}";
		$testee->setUp($json);
	}

	public function testUsername(){
		$this->assertInternalType('string',$this->testee->getUsername(), 'Test if user name is string');
		$this->assertEquals("jdoe",$this->testee->getUsername(), 'Test if first name is jdoe');
	}
	public function testRealName(){
		$this->assertInternalType('string',$this->testee->getFirstName(), 'Test if first name is string');
		$this->assertEquals("John",$this->testee->getFirstName(), 'Test if first name is John');
		
		$this->assertInternalType('string',$this->testee->getLastName(), 'Test if last name is string');
		$this->assertEquals("Doe",$this->testee->getLastName(), 'Test if last name is Doe');
	}
	public function testDateJoined(){
		$this->assertNotEquals(false,$this->testee->getDateJoined('time'), 'Test if Timestamp of date joined isn ot false');
		$this->assertEquals("2010-10-08 12:00:17",$this->testee->getDateJoined('string'), 'Test if Timestamp of date is correct');
	}
	public function testIsLoggedIn(){
		$this->assertInternalType("bool",$this->testee->isLoggedIn(), 'Test if user is logged in');
	}
}