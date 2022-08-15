<?php
// file: model/Recipe.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class Recipe
*
* Represents a Recipe in the blog. A Recipe was written by an
* specific User (author) and contains a list of Comments
*
* @author lipido <lipido@gmail.com>
*/
class Recipe {

	/**
	* The id of this Recipe
	* @var string
	*/
	private $id;

	/**
	* The title of this Recipe
	* @var string
	*/
	private $title;

	/**
	* The content of this Recipe
	* @var string
	*/
	private $content;

	/**
	* The author of this Recipe
	* @var User
	*/
	private $author;

	/**
	* The list of comments of this Recipe
	* @var mixed
	*/
	private $comments;

	/**
	 * Cooking time of this recipe
	 * @var int
	 */
	private $cooking_time;

	/**
	 * image_url of this recipe
	 * @var string
	 */
	private $image_url;
	/**
	 * current date
	 * @var string
	 */
	private $current_date;
	
	/**
	 * array of ingredients
	 * @var array
	 */
	private $ingredients;

	/**
	 * flag to know if the recipe is favorite or not
	 * @var boolean
	 */
	private $favorite;
	/**
	 * current favs
	 * @var int
	 */
	private $favs;

	 
	

	/**
	* The constructor
	*
	* @param string $id The id of the Recipe
	* @param string $title The id of the Recipe
	* @param string $content The content of the Recipe
	* @param User $author The author of the Recipe
	* @param mixed $comments The list of comments
	* @param int $cooking_time The cooking time of the recipe
	*/
	public function __construct($id=NULL, $title=NULL, $cooking_time=NULL ,$content=NULL, User $author=NULL, $image_url=NULL, $current_date=NULL, array $comments=NULL) {
		$this->id = $id;
		$this->title = $title;
		$this->content = $content;
		$this->cooking_time = $cooking_time;
		$this->author = $author;
		$this->comments = $comments;
		$this->image_url = $image_url;
		$this->current_date = $current_date;
		$this->ingredients = array();
		$this->favs = 0;
		$this->favorite = false;
		
	}

	/**
	* Gets the id of this Recipe
	*
	* @return string The id of this Recipe
	*/
	public function getId() {
		return $this->id;
	}

	/**
	* Gets the title of this Recipe
	*
	* @return string The title of this Recipe
	*/
	public function getTitle() {
		return $this->title;
	}



	/**
	 * Gets the cooking time of this Recipe
	 * 	@return string The cooking time of this Recipe
	 */
	public function getCookingTime() {
		return $this->cooking_time;
	}

	/**
	* Gets the content of this Recipe
	*
	* @return string The content of this Recipe
	*/
	public function getContent() {
		return $this->content;
	}

	/**
	* Sets the content of this Recipe
	*
	* @param string $content the content of this Recipe
	* @return void
	*/
	public function setContent($content) {
		$this->content = $content;
	}
	/**
	* Sets the title of this Recipe
	*
	* @param string $title the title of this Recipe
	* @return void
	*/
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Sets the cooking time of this Recipe
	 * @param string $cooking_time the cooking time of this Recipe
	 */
	public function setCookingTime($cooking_time) {
		$this->cooking_time = $cooking_time;
	}

	/**
	* Gets the author of this Recipe
	*
	* @return User The author of this Recipe
	*/
	public function getAuthor() {
		return $this->author;
	}

	/**
	* Sets the author of this Recipe
	*
	* @param User $author the author of this Recipe
	* @return void
	*/
	public function setAuthor(User $author) {
		$this->author = $author;
	}

	/**
	* Gets the list of comments of this Recipe
	*
	* @return mixed The list of comments of this Recipe
	*/
	public function getComments() {
		return $this->comments;
	}

	/**
	* Sets the comments of the Recipe
	*
	* @param mixed $comments the comments list of this Recipe
	* @return void
	*/
	public function setComments(array $comments) {
		$this->comments = $comments;
	}
	/**
	 * Gets the image_url of the recipe
	 * @return string The image_url of the recipe
	 */
	public function getImageUrl() {
		return $this->image_url;
	}
	/**
	 * Sets the image of the recipe
	 * @param string $image_url The image of the recipe
	 */
	public function setImageUrl($image_url) {
		$this->image_url = $image_url;
	}
	/**
	 * Gets the current date
	 * @return string The current date
	 */
	public function getDate() {
		return $this->current_date;
	}
	/**
	 * Sets the current date
	 * @param string $current_date The current date
	 */
	public function setDate($current_date) {
		$this->current_date = $current_date;
	}

	/**
	 * Gets the ingredients of the recipe
	 * @return array The ingredients of the recipe
	 */
	public function getIngredients() {
		return $this->ingredients;
	}
	/**
	 * Sets the ingredients of the recipe
	 * @param array $ingredients The ingredients of the recipe
	 */
	public function setIngredients(array $ingredients) {
		$this->ingredients = $ingredients;
	}
	/** 
	 * Change the favorite flag
	 * @param boolean $favorite the favorite flag
	 */
	public function setFavourite($favorite) {
		$this->favorite = $favorite;
	}
	/**
	 * Gets the favorite flag
	 * @return boolean The favorite flag
	 */
	public function getFavourite() {
		return $this->favorite;
	}
	/**
	 * Gets the current favs
	 * @return int The current favs
	 */
	public function getFavs() {
		return $this->favs;
	}
	/**
	 * Sets the current favs
	 * @param int $favs The current favs
	 */
	public function setFavs($favs) {
		$this->favs = $favs;
	}
	/**
	 * Increments the current favs
	 */
	public function incrementFavs() {
		$this->favs++;
	}
	/**
	 * Decrements the current favs
	 */
	public function decrementFavs() {
		$this->favs--;
	}
	

	/* Checks if the current instance is valid
	* for being updated in the database.
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForCreate() {
		$errors = array();
		if (strlen(trim($this->title)) == 0 ) {
			$errors["title"] = "title is mandatory";
		}
		$target_file = $this -> getImageUrl();
		$selectedIngredients = $this -> getIngredients();
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		
		#comprueba si una variable contiene numeros y no es vacia
		if (!preg_match("/^[0-9]*$/", $this->cooking_time)) {
			$errors["cooking_time"] = i18n("Cooking time must be a number");
		} else if (strlen(trim($this->cooking_time)) == 0 ) {
			$errors["cooking_time"] = i18n("Cooking time is mandatory");
		}

		if (strlen(trim($this->content)) == 0 ) {
			$errors["content"] = i18n("content is mandatory");
		}
		if ($this->author == NULL ) {
			$errors["author"] = i18n("author is mandatory");
		}
		if (empty($_FILES["image_url"]["tmp_name"]) && empty($_POST["image_url_old"])) {
			$errors["image_url"] = i18n("Image is required");
		}
		if ($_FILES["image_url"]["size"] > 500000) {
			$errors["image_url"] = i18n("Image is too large");
		}
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
			$errors["image_url"] = i18n("Only JPG, JPEG, PNG & GIF files are allowed");
		}
		if($selectedIngredients == null){
			$errors["ingredients"] = i18n("No ingredients selected");
			throw new ValidationException($errors,"Error");
		}
		if (count($errors) > 0) {
			throw new ValidationException($errors,i18n("Invalid image"));
		}

		if (sizeof($errors) > 0){
			throw new ValidationException($errors, i18n("Recipe is not valid"));
		}
	}

	/**
	* Checks if the current instance is valid
	* for being updated in the database.
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	public function checkIsValidForUpdate() {
		$errors = array();
		if (strlen(trim($this->title)) == 0 ) {
			$errors["title"] = "title is mandatory";
		}
		$target_file = $this -> getImageUrl();
		$selectedIngredients = $this -> getIngredients();
		$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
		
		#comprueba si una variable contiene numeros y no es vacia
		if (!preg_match("/^[0-9]*$/", $this->cooking_time)) {
			$errors["cooking_time"] = i18n("Cooking time must be a number");
		} else if (strlen(trim($this->cooking_time)) == 0 ) {
			$errors["cooking_time"] = i18n("Cooking time is mandatory");
		}

		if (strlen(trim($this->content)) == 0 ) {
			$errors["content"] = i18n("content is mandatory");
		}
		if ($this->author == NULL ) {
			$errors["author"] = i18n("author is mandatory");
		}
		if (empty($_FILES["image_url"]["tmp_name"]) && empty($_POST["image_url_old"])) {
			$errors["image_url"] = i18n("Image is required");
		}
		if ($_FILES["image_url"]["size"] > 500000) {
			$errors["image_url"] = i18n("Image is too large");
		}
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
			$errors["image_url"] = i18n("Only JPG, JPEG, PNG & GIF files are allowed");
		}
		if($selectedIngredients == null){
			$errors["ingredients"] = i18n("No ingredients selected");
			throw new ValidationException($errors,"Error");
		}
		if (count($errors) > 0) {
			throw new ValidationException($errors,i18n("Invalid image"));
		}

		if (sizeof($errors) > 0){
			throw new ValidationException($errors, i18n("Recipe is not valid"));
		}
	}
}
