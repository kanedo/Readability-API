<?php
namespace Kanedo;
class Bookmark{
	private $id;
	private $excerpt;
	private $author;
	private $title;
	private $domain;
	private $url;
	private $favorited;
	private $user;
	private $date_added;
	private $tags = array();

	private $request;


	public function __construct(Request $request ){
		$this->request = $request;
	}



	/**
	 * Setup the Bookmark
	 * @param string $json The JSON representation of the bookmark retrieved from the api
	 * @see https://readability.com/developers/api/reader#https://www.readability.com/api/rest/v1#bookmarkRepresentation
	 **/	
	public function setUp($json){
		$parsed = json_decode($json);
		if($parsed != NULL){
			if(isset($parsed->id)){
				$this->setId($parsed->id);
			}
			if(isset($parsed->article)){
				$this->setExcerpt($parsed->article->excerpt);
				$this->setAuthor($parsed->article->author);
				$this->setTitle($parsed->article->title);
				$this->setDomain($parsed->article->domain);
				$this->setUrl($parsed->article->url);
			}
			if(isset($parsed->favorite)){
				$this->setFavorited($parsed->favorite);
			}
			if(isset($parsed->user_id)){
				$this->setUser($parsed->user_id);
			}
			if(isset($parsed->date_added)){
				$this->setDateAdded($parsed->date_added);
			}
			if(isset($parsed->tags) && is_array($parsed->tags)){
				foreach ($parsed->tags as $tag) {
					$this->addTag($tag);
				}
			}
		}else{
			throw new \Exception("misformated json");
			return;
		}
	}
	/**
	 * @return int the Bookmark ID
	 **/
	public function getId(){
		return $this->id;
	}
	public function setId($id){
		if($id !== NULL){
			$this->id = $id;
		}
	}

	public function setUser($id){
		if($id !== NULL){
			$this->user = User::byId($id);
		}
	}

	public function getUser(){
		return $this->user;
	}

	public function getExcerpt(){
		return $this->excerpt;
	}

	public function setExcerpt($value){
		if($value !== NULL){
			$this->excerpt = $value;
		}
	}

	public function getAuthor(){
		return $this->author;
	}

	public function setAuthor($value){
		if($value !== NULL){
			$this->author = $value;
		}
	}

	public function getTitle(){
		return $this->title;
	}

	public function setTitle($value){
		if($value !== NULL){
			$this->title = $value;
		}
	}

	public function getDomain(){
		return $this->domain;
	}

	public function setDomain($value){
		if($value !== NULL){
			$this->domain = $value;
		}
	}

	public function getUrl(){
		return $this->url;
	}
	
	public function setUrl($value){
		if($value !== NULL){
			$this->url = $value;
		}
	}

	public function isFavorited(){
		return ($this->favorited === true)? true : false;
	}

	public function setFavorited($value){
		if($value !== NULL){
			$this->favorited = $value;
		}
	}

	public function addTag($tag){
		if($tag != NULL){
			$this->tags[] = array(
					'id' => $tag->id,
					'text' => $tag->text,
				);
		}
	}

	public function hasTag($tag){
		if(!is_string($tag) && !is_integer($tag)){
			return false;
		}

		$key = (is_string($tag))? "text" : "id";

		foreach($this->tags as $_tag){
			if($_tag[$key] == $tag){
				return true;
			}
		}
		return false;
	}

	public function setDateAdded($date){
		if($date !== NULL){
			$this->date_added = $date;
		}
	}
	public function getDateAdded($format){
		switch ($format) {
			case 'time':
				return strtotime($this->date_added);
			default:
				return $this->date_added;
		}
	}
}