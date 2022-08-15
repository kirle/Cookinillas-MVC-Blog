<?php
// file: model/RecipeMapper.php
require_once(__DIR__."/../core/PDOConnection.php");

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/Recipe.php");
require_once(__DIR__."/../model/Comment.php");

/**
* Class RecipeMapper
*
* Database interface for recipe entities
*
* @author lipido <lipido@gmail.com>
*/
class RecipeMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {
		$this->db = PDOConnection::getInstance();
	}

	/**
	* Retrieves all recipes
	*
	* Note: Comments are not added to the recipe instances
	*
	* @throws PDOException if a database error occurs
	* @return mixed Array of recipe instances (without comments)
	*/
	public function findAll() {
		$stmt = $this->db->query("SELECT * FROM recipes, users WHERE users.alias = recipes.author");
		$recipes_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$recipes = array();

		foreach ($recipes_db as $recipe) {
			$author = new User($recipe["alias"]);
			array_push($recipes, new Recipe($recipe["id"], $recipe["title"],$recipe["cooking_time"], $recipe["content"], $author, $recipe["image_url"]));
		}

		return $recipes;
	}

	/**
	 * Retrieves last 10 recipes order by date
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of recipe instances (without comments)
	 */
	public function findLast10() {
		$stmt = $this->db->query("SELECT * FROM recipes, users WHERE users.alias = recipes.author ORDER BY date DESC LIMIT 10");
		$recipes_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$recipes = array();

		foreach ($recipes_db as $recipe) {
			$author = new User($recipe["alias"]);
			array_push($recipes, new Recipe($recipe["id"], $recipe["title"],$recipe["cooking_time"], $recipe["content"], $author, $recipe["image_url"], $recipe["date"]));
		}

		return $recipes;
	}


	/** 
	 * Gets the current user recipes
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of recipe instances (without comments)
	 */
	public function getCurrentUserRecipes() {
		$stmt = $this->db->prepare("SELECT * FROM recipes, users WHERE users.alias = recipes.author AND users.alias = ? ORDER BY date DESC");
		$stmt->execute(array($_SESSION["currentuser"]));
		$recipes_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$recipes = array();

		foreach ($recipes_db as $recipe) {
			$author = new User($recipe["alias"]);
			array_push($recipes, new Recipe($recipe["id"], $recipe["title"],$recipe["cooking_time"], $recipe["content"], $author, $recipe["image_url"], $recipe["date"]));
		}
		return $recipes;
	}



	/**
	 * Gets list of favs recipes of current user
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of recipe instances (without comments)
	 */
	public function getCurrentUserFavs() {
		$stmt = $this->db->prepare("SELECT * FROM recipes, users WHERE users.alias = recipes.author AND users.alias = ? AND recipes.id IN (SELECT recipe_id FROM favs WHERE author = (SELECT alias FROM users WHERE alias = ?)) ORDER BY date DESC");
		$stmt->execute(array($_SESSION["currentuser"], $_SESSION["currentuser"]));
		$recipes_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$recipes = array();

		foreach ($recipes_db as $recipe) {
			$author = new User($recipe["alias"]);
			array_push($recipes, new Recipe($recipe["id"], $recipe["title"],$recipe["cooking_time"], $recipe["content"], $author, $recipe["image_url"], $recipe["date"]));
		}
		return $recipes;
	}

	/**
	 * Given an array of recipes, returns ids of  ones are favs of current user
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of recipe instances (without comments)
	 */
	public function getCurrentUserFavsIds() {
		$stmt = $this->db->prepare("SELECT recipe_id FROM favs WHERE author = (SELECT alias FROM users WHERE alias = ?)");
		$stmt->execute(array($_SESSION["currentuser"]));
		$recipes_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$recipes = array();

		foreach ($recipes_db as $recipe) {
			array_push($recipes, $recipe["recipe_id"]);
		}
		return $recipes;
	}
	
	


	/**
	 * Gets favs of a recipe with specific id
	 * @param string $id The id of the recipe
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of recipe instances (without comments)
	 */
	public function findFavs($id) {
		$stmt = $this->db->query("SELECT COUNT(*) FROM favs WHERE recipe_id = $id");
		$favs = $stmt->fetch(PDO::FETCH_ASSOC);
		$favs = array_values($favs);
		return $favs[0];
	}

	/**
	 * Adds a fav to a recipe of specific id
	 * @param string $id The id of the recipe
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of recipe instances (without comments)
	 */
	public function addToFavourites($id) {
		$stmt = $this->db->prepare("INSERT INTO favs (recipe_id, author) VALUES (?, ?)");
		$stmt->execute(array($id, $_SESSION["currentuser"]));
	}

	/** 
	 * Deletes a fav from a recipe of specific id
	 * @param string $id The id of the recipe
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of recipe instances (without comments)
	 */
	public function deleteFromFavourites($id) {
		$stmt = $this->db->prepare("DELETE FROM favs WHERE recipe_id = ? AND author = ?");
		$stmt->execute(array($id, $_SESSION["currentuser"]));
	}

	/**
	 * Checks if a recipe is in favourites
	 * @param string $id The id of the recipe
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of recipe instances (without comments)
	 */
	public function isInFavs($id) {
		$stmt = $this->db->query("SELECT COUNT(*) FROM favs WHERE recipe_id = $id AND author = '".$_SESSION["currentuser"]."'");
		$favs = $stmt->fetch(PDO::FETCH_ASSOC);
		$favs = array_values($favs);
		return $favs[0];
	}

	/**
	* Loads a recipe from the database given its id
	*
	* Note: Comments are not added to the recipe
	*
	* @throws PDOException if a database error occurs
	* @return recipe The recipe instances (without comments). NULL
	* if the recipe is not found
	*/
	public function findById($recipeid){
		$stmt = $this->db->prepare("SELECT * FROM recipes WHERE id=?");
		$stmt->execute(array($recipeid));
		$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

		if($recipe != null) {
			return new recipe(
			$recipe["id"],
			$recipe["title"],
			$recipe["cooking_time"],
			$recipe["content"],
			new User($recipe["author"]),
			$recipe["image_url"]);
		} else {
			return NULL;
		}
	}

	/**
	* Loads a recipe from the database given its id
	*
	* It includes all the comments
	*
	* @throws PDOException if a database error occurs
	* @return recipe The recipe instances (without comments). NULL
	* if the recipe is not found
	*/
	public function findByIdWithComments($recipeid){
		$stmt = $this->db->prepare("SELECT
			R.id as 'recipe.id',
			R.title as 'recipe.title',
			R.cooking_time as 'recipe.cooking_time',
			R.content as 'recipe.content',
			R.author as 'recipe.author',
			R.image_url as 'recipe.image_url',
			C.id as 'comment.id',
			C.content as 'comment.content',
			C.recipe as 'comment.recipe',
			C.author as 'comment.author'

			FROM recipes R LEFT OUTER JOIN comments C
			ON R.id = C.recipe
			WHERE
			R.id=? ");

			$stmt->execute(array($recipeid));
			$recipe_wt_comments= $stmt->fetchAll(PDO::FETCH_ASSOC);

			if (sizeof($recipe_wt_comments) > 0) {
				$recipe = new Recipe($recipe_wt_comments[0]["recipe.id"],
				$recipe_wt_comments[0]["recipe.title"],
				$recipe_wt_comments[0]["recipe.cooking_time"],
				$recipe_wt_comments[0]["recipe.content"],
				new User($recipe_wt_comments[0]["recipe.author"]),
				$recipe_wt_comments[0]["recipe.image_url"]);
				$comments_array = array();
				if ($recipe_wt_comments[0]["comment.id"]!=null) {
					foreach ($recipe_wt_comments as $comment){
						$comment = new Comment( $comment["comment.id"],
						$comment["comment.content"],
						new User($comment["comment.author"]),
						$comment["comment.recipe"]); // check
						array_push($comments_array, $comment);
					}
				}
				$recipe->setComments($comments_array);

				return $recipe;
			}else {
				return NULL;
			}
		}

		/**
		* Saves a recipe into the database
		*
		* @param recipe $recipe The recipe to be saved
		* @throws PDOException if a database error occurs
		* @return int The mew recipe id
		*/
		public function save(recipe $recipe) {
			$stmt = $this->db->prepare("INSERT INTO recipes(title,cooking_time, content, author,image_url) values (?,?,?,?,?)");
			$stmt->execute(array($recipe->getTitle(), $recipe->getCookingTime(), $recipe->getContent(), $recipe->getAuthor()->getAlias(), $recipe->getImageUrl()));
			return $this->db->lastInsertId();
		}

		/**
		* Updates a recipe in the database
		*
		* @param recipe $recipe The recipe to be updated
		* @throws PDOException if a database error occurs
		* @return void
		*/
		public function update(recipe $recipe) {
			$stmt = $this->db->prepare("UPDATE recipes SET title=?, cooking_time=?, content=?, author=?, image_url=? WHERE id=?");
			$stmt->execute(array($recipe->getTitle(), $recipe->getCookingTime(), $recipe->getContent(), $recipe->getAuthor()->getAlias(), $recipe->getImageUrl(), $recipe->getId()));
		}

		/**
		* Deletes a recipe into the database
		*
		* @param recipe $recipe The recipe to be deleted
		* @throws PDOException if a database error occurs
		* @return void
		*/
		public function delete(Recipe $recipe) {
			$stmt = $this->db->prepare("DELETE from recipes WHERE id=?");
			$stmt->execute(array($recipe->getId()));
		}
	
		

		/**
		 * Function find last ten recipes that contains part of the ingredient name
		 * @param $ingredient
		 * @return array
		 */
		public function findByIngredient($ingredient){
			$stmt = $this->db->prepare("SELECT * FROM recipes R INNER JOIN recipeIngredients RI ON R.id = RI.recipe_id WHERE RI.ingredient_id = (SELECT id FROM ingredient WHERE name LIKE ? LIMIT 1) ORDER BY R.id DESC LIMIT 10");
			$stmt->execute(array('%'.$ingredient.'%'));
			$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$recipe_array = array();
			if (sizeof($recipes) > 0) {
				foreach ($recipes as $recipe){
					$recipe = new Recipe($recipe["id"],
					$recipe["title"],
					$recipe["cooking_time"],
					$recipe["content"],
					new User($recipe["author"]),
					$recipe["image_url"],
					$recipe["date"]

					);
					array_push($recipe_array, $recipe);
				}
			}
			return $recipe_array;
		}

		 

		/** 
		 * Function find last ten recipes that contains part of the title
		 * @param $title
		 * @return array
		 */
		public function findByName($title){
			$stmt = $this->db->prepare("SELECT * FROM recipes WHERE title LIKE '%$title%' ORDER BY id DESC LIMIT 10");
			$stmt->execute();
			$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$recipe_array = array();
			if (sizeof($recipes) > 0) {
				foreach ($recipes as $recipe){
					$recipe = new Recipe($recipe["id"],
					$recipe["title"],
					$recipe["cooking_time"],
					$recipe["content"],
					new User($recipe["author"]),
					$recipe["image_url"],
					$recipe["date"]

					);
					array_push($recipe_array, $recipe);
				}
			}
			return $recipe_array;
		}

		/** 
		 * Function find last then recipes from user alias
		 * @param $alias
		 * @return array
		 */
		public function findByAuthor($alias){
			$stmt = $this->db->prepare("SELECT * FROM recipes WHERE author = (SELECT alias FROM users WHERE alias = ?) ORDER BY id DESC LIMIT 10");
			$stmt->execute(array($alias));
			$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$recipe_array = array();
			if (sizeof($recipes) > 0) {
				foreach ($recipes as $recipe){
					$recipe = new Recipe($recipe["id"],
					$recipe["title"],
					$recipe["cooking_time"],
					$recipe["content"],
					new User($recipe["author"]),
					$recipe["image_url"],
					$recipe["date"]

					);
					array_push($recipe_array, $recipe);
				}
			}
			return $recipe_array;
		}

		
		



		




	

	}
