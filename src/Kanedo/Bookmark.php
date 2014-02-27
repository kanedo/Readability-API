<?php
namespace Kanedo;
class Bookmark{
	public function __construct(){}

	/**
	 * Setup the Bookmark
	 * @param string $json The JSON representation of the bookmark retrieved from the api
	 * @see https://readability.com/developers/api/reader#https://www.readability.com/api/rest/v1#bookmarkRepresentation
	 **/	
	public function setUp($json){
	}
	/**
	 * @return int the Bookmark ID
	 **/
	public function getId(){
		return 0;
	}

	public function getExcerpt(){
		return "";
	}

	public function getAuthor(){
		return "";
	}

	public function getTitle(){
		return "";
	}

	public function getDomain(){
		return "";
	}

	public function isFavorited(){
		return false;
	}

	public function hasTag($tag){
		return false;
	}

	public function getDateAdded($format){
		switch ($format) {
			case 'value':
				return strtotime("");
			default:
				return "";
		}
	}

	/**
	 * Get the user
	 * @return \Kanedo\User The Userobject
	 **/
	public function getUser(){
		return NULL;
	}
}