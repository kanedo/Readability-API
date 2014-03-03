<?php
namespace Kanedo;

class BookmarktsTest extends \PHPUnit_Framework_Testcase {

	public function testGetByID(){
		$request = $this->getMockBuilder('\\Kanedo\\Request')->disableOriginalConstructor()->getMock();
		$request->expects($this->any())
				->method('request')
				->will($this->returnValue("{\"id\" : 1}"));

		$bookmarks = new Bookmarks($request);
		$this->assertInstanceOf('\\Kanedo\Bookmark', $bookmarks->getByID("1"), 'Test if getBookmarksById returns instance of Kanedo\Bookmark');
		$this->assertEquals(1 ,$bookmarks->getByID("1")->getId(), 'Test if correct Bookmark was fetched');
	}

	public function testGetByIDWithResourceNotFound(){
		//TokenResponseException
		$request = $this->getMockBuilder('\\Kanedo\\Request')->disableOriginalConstructor()->getMock();
		$request->expects($this->any())
				->method('request')
				->will($this->throwException(new \OAuth\Common\Http\Exception\TokenResponseException("Resouce not found", 404)));

		$bookmarks = new Bookmarks($request);
		$this->assertFalse($bookmarks->getByID(1), 'Test if Bookmarks returns false if Resource is not found');
	}

	public function testFilterSetFavorited(){
		$request = $this->getMockBuilder('\\Kanedo\\Request')->disableOriginalConstructor()->getMock();
		$bookmarks = new Bookmarks($request);
		$this->assertInstanceOf('\\Kanedo\\Bookmarks',$bookmarks->setFavorited(true), 'Test if Bookmarks setFavorited is chainable');
	}

	public function testFilterSetFavoritedSince(){
		$request = $this->getMockBuilder('\\Kanedo\\Request')->disableOriginalConstructor()->getMock();
		$bookmarks = new Bookmarks($request);
		$this->assertInstanceOf('\\Kanedo\\Bookmarks',$bookmarks->setFavoritedSince(time()), 'Test if Bookmarks setFavoritedSince is chainable');
		$this->setExpectedException('\InvalidArgumentException');
		$bookmarks->setFavoritedUntil(1);
		$bookmarks->setFavoritedSince(time());

	}

	public function testFilterSetFavoritedUntil(){
		$request = $this->getMockBuilder('\\Kanedo\\Request')->disableOriginalConstructor()->getMock();
		$bookmarks = new Bookmarks($request);
		$this->assertInstanceOf('\\Kanedo\\Bookmarks',$bookmarks->setFavoritedUntil(time()), 'Test if Bookmarks setFavoritedUntil is chainable');
		$this->setExpectedException('\InvalidArgumentException');
		$bookmarks->setFavoritedSince(time());
		$bookmarks->setFavoritedUntil(1);

	}
}