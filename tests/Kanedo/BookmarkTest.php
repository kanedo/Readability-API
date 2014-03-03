<?php
namespace Kanedo;
class BookmarkTest extends \PHPUnit_Framework_Testcase {
	private $testee;
	public function setUp(){
		date_default_timezone_set("Europe/Berlin");
		$request = $this->getMockBuilder('\\Kanedo\\Request')->disableOriginalConstructor()->getMock();


		$this->testee = new Bookmark($request);
		$bookmark = '{
    "user_id": 1,
    "read_percent": "0.13",
    "date_updated": "2010-10-12 11:04:46",
    "favorite": false,
    "article": {
        "domain": "www.newyorker.com",
        "title": "Booted",
        "url": "http://www.newyorker.com/arts/critics/atlarge/2011/04/11/110411crat_atlarge_parks?currentPage=all",
        "lead_image_url": "http://www.newyorker.com/images/2011/04/11/p233/110411_r20743_p233.jpg",
        "author": "Tim Parks",
        "excerpt": "Italian voters used to be charmed by Berlusconi&#x2019;s libertine ways. Anniversaries are uplifting when you have something to &hellip;",
        "direction": "ltr",
        "word_count": 4291,
        "date_published": "2011-04-11 00:00:00",
        "dek": "What really ails Italy?",
        "processed": true,
        "id": "47g6s8e7"
    },
    "id": 75,
    "date_archived": null,
    "date_opened": null,
    "date_added": "2010-10-12 11:04:46",
    "article_href": "/api/rest/v1/articles/86/",
    "date_favorited": null,
    "archive": false,
    "tags": [{"text": "w3", "id": 123}, {"text": "web", "id": 456}]
}';
		$this->testee->setUp($bookmark);
	}

	/**
	 * @expectedException \Exception
	 */
	public function testMisformatedJSON(){
		$request = $this->getMockBuilder('\\Kanedo\\Request')->disableOriginalConstructor()->getMock();

		$testee = new Bookmark($request);		
		$json = "{this is wroooong: lols'}";
		$testee->setUp($json);
	}
	public function testBookmarkHasID(){
		$this->assertNotEquals(NULL,$this->testee->getId(), 'Test that the Bookmark ID is not NULL');
	}
	public function testBookmarkID(){
		$this->assertEquals(75,$this->testee->getId(), 'Test that Bookmark ID is 75');
	}

	public function testUser(){
		$this->assertInstanceOf('\Kanedo\User',$this->testee->getUser(), 'Test that User is Instance of \Kanedo\User');
	}

	public function testExcerpt(){
		$this->assertEquals("Italian voters used to be charmed by Berlusconi&#x2019;s libertine ways. Anniversaries are uplifting when you have something to &hellip;",
			$this->testee->getExcerpt(),
			'Test Bookmark Excerpt is correct');
	}

	public function testAuthor(){
		$this->assertEquals("Tim Parks",$this->testee->getAuthor(), 'Testing Bookmark author');
	}

	public function testTitle(){
		$this->assertEquals("Booted",$this->testee->getTitle(), 'Test Bookmark title');
	}

	public function testDomain(){
		$this->assertEquals("www.newyorker.com",$this->testee->getDomain(), 'Test Bookmark Domain');
	}

	public function testDate(){
		$this->assertEquals(strtotime("2010-10-12 11:04:46"),$this->testee->getDateAdded('time'), 'Test Date Added as Timestamp');
		$this->assertEquals(("2010-10-12 11:04:46"),$this->testee->getDateAdded('string'), 'Test Date Added as String');
	}

	public function testIsFavorited(){
		$this->assertFalse($this->testee->isFavorited(), 'Test is Bookmark Favorited');
	}

	public function testHasTags(){
		$this->assertTrue($this->testee->hasTag('w3'), 'Test Bookmark has Tag "w3"');
		$this->assertTrue($this->testee->hasTag('web'), 'Test Bookmark has Tag "web"');
		$this->assertFalse($this->testee->hasTag('ThisTagDoesNotExist'), 'Test Bookmark has no Tag "ThisTagDoesNotExist"');
	}

}