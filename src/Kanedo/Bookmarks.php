<?php
namespace Kanedo;
use \OAuth\Common\Http\Exception\TokenResponseException;
class Bookmarks {

	private $request;

	private $filters = array();

	public function __construct(Request $request){
		$this->request = $request;
	}

	public function getById( $id ){
		try{
			$response = $this->request->request("/bookmarks/{$id}");
		}catch(TokenResponseException $e){
			//log error
			return false;
		}

		$bookmark = new Bookmark($this->request);
		$bookmark->setUp($response);
		return $bookmark;
	}

	public function setFavorited($favorited){
		if(!is_bool($favorited)){
			throw new \InvalidArgumentException("Expected Bool to be given");
		}

		$this->filters['favorited'] = ($favorited === true)? true : false;
		return $this;
	}

	public function setFavoritedSince( $time ){
		if(array_key_exists('favorited_until', $this->filters) && $time >= $this->filters['favorited_until']){
			throw new \InvalidArgumentException("Expected favorited_since to be lower then favorited_until");
		}
		$this->setFavorited(true);
		$this->filters['favorited_since'] = $time;
		return $this;
	}

	public function setFavoritedUntil( $time ){
		if(array_key_exists('favorited_since', $this->filters) && $time <= $this->filters['favorited_since']){
			throw new \InvalidArgumentException("Expected favorited_until to be greater then favorited_since");
		}
		$this->setFavorited(true);
		$this->filters['favorited_until'] = $time;
		return $this;
	}
}