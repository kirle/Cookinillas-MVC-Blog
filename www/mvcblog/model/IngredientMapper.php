<?php
// file: model/IngredientMapper.php
require_once(__DIR__."/../core/PDOConnection.php");

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/Recipe.php");
require_once(__DIR__."/../model/Ingredient.php");

require_once(__DIR__."/../model/Comment.php");

/**
* Class IngredientMapper
*
* Database interface for recipe entities
*
* @author kirle
*/
class IngredientMapper {
	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {
		$this->db = PDOConnection::getInstance();
	}

	/**
	 * Gets all ingredients
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of ingredients
	 */
	public function getAllIngredients() {
		$stmt = $this->db->query("SELECT * FROM ingredient");
		$ingredients_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$ingredients = array();
		foreach ($ingredients_db as $ingredient) {
			array_push($ingredients, array($ingredient["id"],$ingredient["name"]));

		}
		return $ingredients;
	}
	/**
	 * find ingredient by id
	 * @param int $id
	 * @throws PDOException if a database error occurs
	 * @return Ingredient
	 */
	public function findById($id) {
		$stmt = $this->db->prepare("SELECT * FROM ingredient WHERE id=?");
		$stmt->execute(array($id));
		$ingredient_db = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($ingredient_db != null) {
			$ingredient = new Ingredient($ingredient_db["id"], $ingredient_db["name"]);
			return $ingredient;
		} else {
			return null;
		}
	}

	/**
	 * Adds a new ingredient to the database
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of ingredients
	 */
	public function addIngredient($name) {
		$stmt = $this->db->prepare("INSERT INTO ingredient (name) VALUES (?)");
		$stmt->execute(array($name));
		return $this->getAllIngredients();
	}

	/** 
	 * Checks if ingredient exists on db
	 * @param string $name
	 * @throws PDOException if a database error occurs
	 * @return boolean
	 */
	public function ingredientExists($name) {
		$stmt = $this->db->prepare("SELECT * FROM ingredient WHERE name=?");
		$stmt->execute(array($name));
		$ingredient_db = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($ingredient_db != null) {
			return true;
		} else {
			return false;
		}
	}

	 
	/**
	 * Adds a ingredient to a recipe
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of ingredients
	 */
	public function addIngredientToRecipe($recipe_id, $ingredient_id) {
		$stmt = $this->db->prepare("INSERT INTO recipeIngredients (recipe_id, ingredient_id) VALUES (?, ?)");
		$stmt->execute(array($recipe_id, $ingredient_id));
		return $this->getAllIngredients();
	}

	public function addIngredientsToRecipe($recipe_id, $ingredients) {
		foreach ($ingredients as $ingredient) {
			$stmt = $this->db->prepare("INSERT INTO recipeIngredients (recipe_id, ingredient_id, amount) VALUES (?,?,?)");
			$stmt->execute(array($recipe_id, $ingredient["id"], $ingredient["amount"]));
		}
		return $this->getAllIngredients();
	}

	/**
	 * Gets all ingredients of a recipe
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of ingredients
	 */
	public function getIngredientsOfRecipe($recipe_id) {
		$stmt = $this->db->prepare("SELECT ingredient_id, name, amount FROM recipeIngredients, ingredient 
			WHERE recipe_id = ? AND recipeIngredients.ingredient_id = ingredient.id");
		$stmt->execute(array($recipe_id));
		$ingredients_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$ingredients = array();

		
		foreach ($ingredients_db as $ingredient) {
			array_push($ingredients, array($ingredient["ingredient_id"],$ingredient["name"],$ingredient["amount"]));

		}
		return $ingredients;
	}
	/**
	 * Update all ingredients of a recipe
	 * @throws PDOException if a database error occurs
	 * @return mixed Array of ingredients
	 */
	public function updateIngredientsOfRecipe($recipe_id, $ingredients) {
		$stmt = $this->db->prepare("DELETE FROM recipeIngredients WHERE recipe_id = ?");
		$stmt->execute(array($recipe_id));
		$this->addIngredientsToRecipe($recipe_id, $ingredients);
		return $this->getAllIngredients();
	}

	
	

}