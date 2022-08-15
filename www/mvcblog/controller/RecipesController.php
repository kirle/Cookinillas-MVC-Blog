<?php
//file: controller/recipeController.php

require_once(__DIR__."/../model/Comment.php");
require_once(__DIR__."/../model/Recipe.php");
require_once(__DIR__."/../model/Ingredient.php");

require_once(__DIR__."/../model/RecipeMapper.php");
require_once(__DIR__."/../model/IngredientMapper.php");

require_once(__DIR__."/../model/User.php");

require_once(__DIR__."/../core/ViewManager.php");
require_once(__DIR__."/../controller/BaseController.php");

/**
* Class recipesController
*
* Controller to make a CRUDL of recipes entities
*
* @author lipido <lipido@gmail.com>
*/
class RecipesController extends BaseController {

	/**
	* Reference to the RecipeMapper to interact
	* with the database
	*
	* @var RecipeMapper
	*/
	private $RecipeMapper;
	private $IngredientMapper;

	public function __construct() {
		parent::__construct();

		$this->RecipeMapper = new RecipeMapper();
		$this->IngredientMapper = new IngredientMapper();
		

	}

	/**
	* Action to list recipes
	*
	* Loads all the recipes from the database.
	* No HTTP parameters are needed.
	*
	* The views are:
	* <ul>
	* <li>recipes/index (via include)</li>
	* </ul>
	*/
	public function index() {

		// obtain the data from the database
		//$recipes = $this->RecipeMapper->findAll();
		$recipes = $this->RecipeMapper->findLast10();

		foreach ($recipes as $recipe) {
			$ingredients = $this->IngredientMapper->getIngredientsOfRecipe($recipe->getId());
			$names = array();
			foreach ($ingredients as $ingredient) {
				array_push($names, $ingredient[1]);
			}
			$recipe->setIngredients($names);
			//get the favs of that recipe
			$favs = $this->RecipeMapper->findFavs($recipe->getId());
			$recipe->setFavs($favs);


		}
		// if current user is logged
		if (isset($_SESSION["currentuser"])) {
			$favs = $this->RecipeMapper->getCurrentUserFavsIds($recipes);
			// put the array in the view
			$this->view->setVariable("favs", $favs);
		}
		
		// put the array containing recipe object to the view
		$this->view->setVariable("recipes", $recipes);

		// render the view (/view/recipes/index.php)
		$this->view->render("recipes", "index");
	}

	/**
	 * Action to search a recipe by ingredient
	 * 
	 */
	public function search() {
		$recipes = array();
		// filtering by action (radio button)
		if (isset($_POST["search_action"])) {
			$action = $_POST["search_action"];
			if ($action == "searchByIngredient") {
				// filtering by ingredient
				if (isset($_POST["ingredient"]) and $_POST["ingredient"] != "") {
					$ingredient = $_POST["ingredient"];
					$recipes = $this->RecipeMapper->findByIngredient($ingredient);
		
					$this->view->setVariable("recipes", $recipes);
					$error_name = "ingredient";

				}
			} else if ($action == "searchByName") {
				// filtering by name
				if (isset($_POST["ingredient"])) {
					$name = $_POST["ingredient"];
					$recipes = $this->RecipeMapper->findByName($name);
					$this->view->setVariable("recipes", $recipes);
					$error_name = "title";
				}
			} else if($action == "searchByAuthor") {
				// filtering by author
				if (isset($_POST["ingredient"])) {
					$author = $_POST["ingredient"];
					$recipes = $this->RecipeMapper->findByAuthor($author);
					$this->view->setVariable("recipes", $recipes);
					$error_name = "author";
				}
			}
		}
	
		if (count($recipes) == 0) {
			$recipes = $this->RecipeMapper->findLast10();
			$errors = array();

			if ($_POST["ingredient"] != "") {
				$errors["ingredient"] = i18n("No recipes found with this"." ".$error_name);
				$this->view->setVariable("errors", $errors);
			}
			
			$this->view->setVariable("recipes", $recipes);
			
		}

		foreach ($recipes as $recipe) {
			//get the favs of that recipe
			$favs = $this->RecipeMapper->findFavs($recipe->getId());
			$recipe->setFavs($favs);


		}
		// get favs
		$favs = $this->RecipeMapper->getCurrentUserFavsIds($recipes);
		$this->view->setVariable("favs", $favs);

		$this->view->render("recipes", "index");
	}


	/**
	* Action to view a given recipe
	*
	* This action should only be called via GET
	*
	* The expected HTTP parameters are:
	* <ul>
	* <li>id: Id of the recipe (via HTTP GET)</li>
	* </ul>
	*
	* The views are:
	* <ul>
	* <li>recipes/view: If recipe is successfully loaded (via include).	Includes these view variables:</li>
	* <ul>
	*	<li>recipe: The current recipe retrieved</li>
	*	<li>comment: The current Comment instance, empty or
	*	being added (but not validated)</li>
	* </ul>
	* </ul>
	*
	* @throws Exception If no such recipe of the given id is found
	* @return void
	*
	*/



	public function view(){
		if (!isset($_GET["id"])) {
			throw new Exception("id is mandatory");
		}

		$recipeid = $_GET["id"];

		// find the recipe object in the database
		$recipe = $this->RecipeMapper->findByIdWithComments($recipeid);
		$recipeIngredients = $this->IngredientMapper->getIngredientsOfRecipe($recipeid);
		$favs = $this->RecipeMapper->findFavs($recipeid);
		//if current user is logged
		if (isset($_SESSION["currentuser"])) {
			$isInFavs = $this->RecipeMapper->isInFavs($recipeid);
			$this->view->setVariable("isInFavs", $isInFavs);

		}
		

		if ($recipe == NULL) {
			throw new Exception("no such recipe with id: ".$recipeid);
		}


		// put the recipe object to the view
		$this->view->setVariable("recipe", $recipe);
		$this->view->setVariable("favs", $favs);
		$this->view->setVariable("recipeIngredients", $recipeIngredients);


		// check if comment is already on the view (for example as flash variable)
		// if not, put an empty Comment for the view
		$comment = $this->view->getVariable("comment");
		$this->view->setVariable("comment", ($comment==NULL)?new Comment():$comment);

		// render the view (/view/recipes/view.php)
		$this->view->render("recipes", "view");

	}


	/**
	 * ----------------
	 * -- ADD RECIPE --
	 * ----------------
	* Action to add a new recipe
	*
	* When called via GET, it shows the add form
	* When called via recipe, it adds the recipe to the
	* database
	*
	* The expected HTTP parameters are:
	* <ul>
	* <li>title: Title of the recipe (via HTTP recipe)</li>
	* <li>content: Content of the recipe (via HTTP recipe)</li>
	* </ul>
	*
	* The views are:
	* <ul>
	* <li>recipes/add: If this action is reached via HTTP GET (via include)</li>
	* <li>recipes/index: If recipe was successfully added (via redirect)</li>
	* <li>recipes/add: If validation fails (via include). Includes these view variables:</li>
	* <ul>
	*	<li>recipe: The current recipe instance, empty or
	*	being added (but not validated)</li>
	*	<li>errors: Array including per-field validation errors</li>
	* </ul>
	* </ul>
	* @throws Exception if no user is in session
	* @return void
	*/
	
	public function add() {


		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Adding recipes requires login");
		}
	
		$recipe = new Recipe();

		if(isset($_POST['addIngredient']) && $_POST['addIngredient'] != "" ){
			// to-do: check if ingredient is valid
			if($this->IngredientMapper->ingredientExists($_POST['newIngredient'])){
				$errors["newIngredient"] = i18n("Ingredient already exists");
			} else{
				$this->IngredientMapper->addIngredient($_POST['newIngredient']);

			}
			//$recipe->setIngredient($ingredient);
		}

		// action to add ingredient to recipe unconfirmed

		if (isset($_POST['selectedIngredients'])) {
			$selectedIngredients2 =  $_POST['selectedIngredients'];
			$selectedIngredients = array();
			for ($i = 0; $i < count($selectedIngredients2); $i = $i + 3) 
			{
				array_push($selectedIngredients, array('id'=> $selectedIngredients2[$i],'name' => $selectedIngredients2[$i+1], 'amount' => $selectedIngredients2[$i+2]));
			}

		} else{
			$selectedIngredients = array();

		}


		if(isset($_POST['addIngredientToRecipe'])){
			if($selectedIngredients == NULL){
				$selectedIngredients = array();
			}
			//to-do: check if valid
			$ingredientid = $_POST['selected'];
			$ingredientName = $this->IngredientMapper->findById($ingredientid)->getName();
			
			$ingredientAmount = $_POST['amount'];
			array_push($selectedIngredients, array('id'=> $ingredientid,'name' => $ingredientName, 'amount' => $ingredientAmount));
			$this->view->setVariable("selectedIngredients", $selectedIngredients);
		}	
		if(isset($_POST['removeIngredientFromRecipe'])){
			// to-do: improve removing
			// actually filling array with every ingredient that doesnt match the id
			$ingredientid = $_POST['ingredient_id_remove'];
			$selectedIngredients2 =  $_POST['selectedIngredients'];
			$selectedIngredients = array();
			for ($i = 0; $i < count($selectedIngredients2); $i = $i + 3) 
			{
				if($selectedIngredients2[$i] != $ingredientid){
					array_push($selectedIngredients, array('id'=> $selectedIngredients2[$i],'name' => $selectedIngredients2[$i+1], 'amount' => $selectedIngredients2[$i+2]));
				}
			}
			$this->view->setVariable("selectedIngredients", $selectedIngredients);
		}	
		

		if(sizeof($_POST) > 0){
			// populate the recipe object with data form the form

			$recipe->setTitle($_POST["title"]);
			$recipe->setContent($_POST["content"]);
			$recipe->setCookingTime($_POST["cooking_time"]);
	
			$ingredients = $this->IngredientMapper->getAllIngredients();
			// $recipe -> setIngredients($ingredients);

			// image
			// if there is an image name in the form, it means that there is an image to upload
			// else check if there is and old image uploaded and use that 
			$target_dir = "uploads/";
			if($_FILES["image_url"]["name"] != ""){
				$target_file = $target_dir . basename($_FILES["image_url"]["name"]);
			} else if($_POST["image_url_old"] != ""){
				$target_file = $target_dir . basename($_POST["image_url_old"]);
			} else {
				$target_file = "";
			}

			$recipe->setImageUrl($target_file);
	
		}
		

		// The user of the recipe is the currentUser (user in session)
		$recipe->setAuthor($this->currentUser);
		$recipe->setIngredients($selectedIngredients);        
		if (isset($_POST["submit"])) { // reaching via HTTP recipe...

			try {
				$errors = array();
				
				// validate recipe object
				$recipe->checkIsValidForCreate(); // if it fails, ValidationException
			
				if (!move_uploaded_file($_FILES["image_url"]["tmp_name"], $target_file)) {
					$errors["image_url"] = i18n("Sorry, there was an error uploading your file. Reload it again");
					throw new ValidationException($errors,"Error uploading image");
				}
				//to-do : this is a ugly patch to inform user, 
				//need to add some js on client-side or something to keep
				// the selected image
				
				// save the recipe object into the database
				$recipeid = $this->RecipeMapper->save($recipe);

				//throw new ValidationException($errors,$selectedIngredients);
				// Add ingredients to the saved recipe
				
				$this->IngredientMapper->addIngredientsToRecipe($recipeid, $selectedIngredients);
				
				
				// recipe-REDIRECT-GET
				// Everything OK, we will redirect the user to the list of recipes
				// We want to see a message after redirection, so we establish
				// a "flash" message (which is simply a Session variable) to be
				// get in the view after redirection.
				$this->view->setFlash("addedOk");

				// perform the redirection. More or less:
				// header("Location: index.php?controller=recipes&action=index")
				// die();
				$this->view->redirect("recipes", "index");

			}catch(ValidationException $ex) {
				// Get the errors array inside the exepction...
				$errors = $ex->getErrors();
				// And put it to the view as "errors" variable
				$this->view->setVariable("errors", $errors);
			}
		}
		if(isset($errors)){
			$this->view->setVariable("errors", $errors);
		}

		// Put the recipe object visible to the view
		$this->view->setVariable("recipe", $recipe);
		$ingredients = $this->IngredientMapper->getAllIngredients();
		$this->view->setVariable("ingredients", $ingredients);
		$this->view->setVariable("selectedIngredients", $selectedIngredients);
		// render the view (/view/recipes/add.php)
		$this->view->render("recipes", "add");
	}
	

	/**
	* Action to edit a recipe
	*
	* When called via GET, it shows an edit form
	* including the current data of the recipe.
	* When called via recipe, it modifies the recipe in the
	* database.
	*
	* The expected HTTP parameters are:
	* <ul>
	* <li>id: Id of the recipe (via HTTP recipe and GET)</li>
	* <li>title: Title of the recipe (via HTTP recipe)</li>
	* <li>content: Content of the recipe (via HTTP recipe)</li>
	* </ul>
	*
	* The views are:
	* <ul>
	* <li>recipes/edit: If this action is reached via HTTP GET (via include)</li>
	* <li>recipes/index: If recipe was successfully edited (via redirect)</li>
	* <li>recipes/edit: If validation fails (via include). Includes these view variables:</li>
	* <ul>
	*	<li>recipe: The current recipe instance, empty or being added (but not validated)</li>
	*	<li>errors: Array including per-field validation errors</li>
	* </ul>
	* </ul>
	* @throws Exception if no id was provided
	* @throws Exception if no user is in session
	* @throws Exception if there is not any recipe with the provided id
	* @throws Exception if the current logged user is not the author of the recipe
	* @return void
	*/

	public function edit() {
		if (!isset($_REQUEST["id"])) {
			throw new Exception("A recipe id is mandatory");
		}

		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Editing recipes requires login");
		}


		// Get the recipe object from the database
		$recipeid = $_REQUEST["id"];
		$recipe = $this->RecipeMapper->findById($recipeid);

		// Does the recipe exist?
		if ($recipe == NULL) {
			throw new Exception("no such recipe with id: ".$recipeid);
		}

		// Check if the recipe author is the currentUser (in Session)
		if ($recipe->getAuthor() != $this->currentUser) {
			throw new Exception("logged user is not the author of the recipe id ".$recipeid);
		}
		$ingredients = $this->IngredientMapper->getAllIngredients();
		if(isset($_POST['addIngredient']) && $_POST['addIngredient'] != ""){
			// to-do: check if ingredient is valid
			$this->IngredientMapper->addIngredient($_POST['newIngredient']);
			//$recipe->setIngredient($ingredient);
		}
		// action to add ingredient to recipe unconfirmed
		if (isset($_POST['selectedIngredients']) ) {
			$selectedIngredients2 =  $_POST['selectedIngredients'];
			$selectedIngredients = array();
			for ($i = 0; $i < count($selectedIngredients2); $i = $i + 3) 
			{
				array_push($selectedIngredients, array('id'=> $selectedIngredients2[$i],'name' => $selectedIngredients2[$i+1], 'amount' => $selectedIngredients2[$i+2]));
			}

		} 
		// if this is the first time we are showing the form for editing, there is no post data
		else if(!isset($_POST['id'])){
			$selectedIngredients2 = $this->IngredientMapper->getIngredientsOfRecipe($recipeid);
			$selectedIngredients = array();
			foreach ($selectedIngredients2 as $ingredient) {
				array_push($selectedIngredients, array('id'=> $ingredient[0],'name' => $ingredient[1], 'amount' => $ingredient[2]));
			}		
		} else {
			$selectedIngredients = array();
		}


		if(isset($_POST['addIngredientToRecipe'])){
			if($selectedIngredients == NULL){
				$selectedIngredients = array();
			}
			//to-do: check if valid
			$ingredientid = $_POST['selected'];
			$ingredientName = $this->IngredientMapper->findById($ingredientid)->getName();
			
			$ingredientAmount = $_POST['amount'];
			array_push($selectedIngredients, array('id'=> $ingredientid,'name' => $ingredientName, 'amount' => $ingredientAmount));
			$this->view->setVariable("selectedIngredients", $selectedIngredients);
		}	
		if(isset($_POST['removeIngredientFromRecipe'])){
			// to-do: improve removing
			// actually filling array with every ingredient that doesnt match the id
			$ingredientid = $_POST['ingredient_id_remove'];
			$selectedIngredients2 =  $_POST['selectedIngredients'];
			$selectedIngredients = array();
			for ($i = 0; $i < count($selectedIngredients2); $i = $i + 3) 
			{
				if($selectedIngredients2[$i] != $ingredientid){
					array_push($selectedIngredients, array('id'=> $selectedIngredients2[$i],'name' => $selectedIngredients2[$i+1], 'amount' => $selectedIngredients2[$i+2]));
				}
			}
			$this->view->setVariable("selectedIngredients", $selectedIngredients);
		}	
		

		if (isset($_POST["submit"])) { // reaching via HTTP recipe...

			// populate the recipe object with data form the form
			$recipe->setTitle($_POST["title"]);
			$recipe->setContent($_POST["content"]);
			$recipe->setCookingTime($_POST["cooking_time"]);
			$recipe->setIngredients($selectedIngredients);
			
			

			try {
				// validate recipe object
				$recipe->checkIsValidForUpdate(); // if it fails, ValidationException

				// update the recipe object in the database
				$this->RecipeMapper->update($recipe);
				$this->IngredientMapper->updateIngredientsOfRecipe($recipeid, $selectedIngredients);

				// recipe-REDIRECT-GET
				// Everything OK, we will redirect the user to the list of recipes
				// We want to see a message after redirection, so we establish
				// a "flash" message (which is simply a Session variable) to be
				// get in the view after redirection.
				$this->view->setFlash("updatedOk");

				// perform the redirection. More or less:
				// header("Location: index.php?controller=recipes&action=index")
				// die();
				$this->view->redirect("recipes", "index");

			}catch(ValidationException $ex) {
				// Get the errors array inside the exepction...
				$errors = $ex->getErrors();
				// And put it to the view as "errors" variable
				$this->view->setVariable("errors", $errors);
			}
		}

		// Put the recipe object visible to the view
		$this->view->setVariable("recipe", $recipe);
		$this->view->setVariable("ingredients", $ingredients);
		$this->view->setVariable("selectedIngredients", $selectedIngredients);
		// render the view (/view/recipes/add.php)
		$this->view->render("recipes", "edit");
	}



	/**
	* Action to delete a recipe
	*
	* This action should only be called via HTTP recipe
	*
	* The expected HTTP parameters are:
	* <ul>
	* <li>id: Id of the recipe (via HTTP recipe)</li>
	* </ul>
	*
	* The views are:
	* <ul>
	* <li>recipes/index: If recipe was successfully deleted (via redirect)</li>
	* </ul>
	* @throws Exception if no id was provided
	* @throws Exception if no user is in session
	* @throws Exception if there is not any recipe with the provided id
	* @throws Exception if the author of the recipe to be deleted is not the current user
	* @return void
	*/
	public function delete() {
		if (!isset($_POST["id"])) {
			throw new Exception("id is mandatory");
		}
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Editing recipes requires login");
		}
		
		// Get the recipe object from the database
		$recipeid = $_REQUEST["id"];
		$recipe = $this->RecipeMapper->findById($recipeid);

		// Does the recipe exist?
		if ($recipe == NULL) {
			throw new Exception("no such recipe with id: ".$recipeid);
		}

		// Check if the recipe author is the currentUser (in Session)
		if ($recipe->getAuthor() != $this->currentUser) {
			throw new Exception("recipe author is not the logged user");
		}

		// Delete the recipe object from the database
		$this->RecipeMapper->delete($recipe);

		// recipe-REDIRECT-GET
		// Everything OK, we will redirect the user to the list of recipes
		// We want to see a message after redirection, so we establish
		// a "flash" message (which is simply a Session variable) to be
		// get in the view after redirection.
		//$this->view->setFlash(sprintf(i18n("recipe \"%s\" successfully deleted."),$recipe ->getTitle()));
		$this->view->setFlash("deletedOk");

		// perform the redirection. More or less:
		// header("Location: index.php?controller=recipes&action=index")
		// die();
		$this->view->redirect("recipes", "index");

	}

	/**
	 * Action to list the recipes of the current user
	 * This action should only be called via HTTP recipe
	 * @throws Exception if no user is in session
	 * @return void
	 */
	public function getOwnRecipes() {
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Editing recipes requires login");
		}
		$recipes = $this->RecipeMapper->getCurrentUserRecipes();
		$favs = $this->RecipeMapper->getCurrentUserFavsIds($recipes);
		foreach ($recipes as $recipe) {
			$recipe->setFavs($this->RecipeMapper->findFavs($recipe->getId()));
		}
		$this->view->setVariable("favs", $favs);
		$this->view->setVariable("recipes", $recipes);
		$this->view->render("recipes", "getOwnRecipes");
	}

	/**
	 * Action to list the home  of the current user, will show favorite recipes
	 * or the last recipes added if not favorites.
	 * This action should only be called via HTTP recipe
	 * @throws Exception if no user is in session
	 * @return void
	 */
	public function home() {
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Requires login");
		}
		$favs = $this->RecipeMapper->getCurrentUserFavs();
		if (count($favs) == 0) {
			$recipes = $this->RecipeMapper->findLast10();
			$showingFavs = false;
		} else {
			$recipes = $favs;
			$showingFavs = true;

		}
		foreach ($recipes as $recipe) {
			$recipe->setFavs($this->RecipeMapper->findFavs($recipe->getId()));
		}

		$userFavs = $this->RecipeMapper->getCurrentUserFavsIds($recipes);
		$this->view->setVariable("favs", $userFavs);
		$this->view->setVariable("recipes", $recipes);
		$this->view->setVariable("showingFavs", $showingFavs);

		$this->view->render("recipes", "home");
	}


	/** Action add a recipe to favourites
	 * This action should only be called via HTTP recipe
	 * The expected HTTP parameters are:
	 * <ul>
	 * <li>id: Id of the recipe (via HTTP recipe)</li>
	 * </ul>
	 * The views are:
	 * <ul>
	 * <li>recipes/index: If recipe was successfully added to favourites (via redirect)</li>
	 * </ul>
	 * @throws Exception if no id was provided
	 * @throws Exception if no user is in session
	 * @throws Exception if there is not any recipe with the provided id
	 * @throws Exception if the author of the recipe to be added to favourites is not the current user
	 * @return void
	 */
	public function addToFavourites() {
		if (!isset($_GET["id"])) {
			throw new Exception("id is mandatory");
		}
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Adding recipes to favourites requires login");
		}
		// get the recipe object from the database
		$recipeid = $_GET["id"];
		$recipe = $this->RecipeMapper->findById($recipeid);
		$recipe->setFavourite(true);

		// Does the recipe exist?
		if ($recipe == NULL) {
			throw new Exception("no such recipe with id: ".$recipeid);
		}
		// Check if the recipe author is the currentUser (in Session)
		// if ($recipe->getAuthor() != $this->currentUser) {
		// 	throw new Exception("recipe author is not the logged user");
		// }
		// Add the recipe to favourites
		$this->RecipeMapper->addToFavourites($recipeid);
		// recipe-REDIRECT-GET
		// Everything OK, we will redirect the user to the list of recipes
		// We want to see a message after redirection, so we establish
		// a "flash" message (which is simply a Session variable) to be
		// get in the view after redirection.
		$this->view->setFlash("addedFavOk");
		
		// perform the redirection. More or less:
		// header("Location: index.php?controller=recipes&action=index")
		// die();
		
		$this->view->redirect("recipes", "index");

	}

	/** 
	 * Action to delete a recipe from favourites
	 * This action should only be called via HTTP recipe
	 * The expected HTTP parameters are:
	 * <ul>
	 * <li>id: Id of the recipe (via HTTP recipe)</li>
	 * </ul>
	 * The views are:
	 * <ul>
	 * <li>recipes/index: If recipe was successfully deleted from favourites (via redirect)</li>
	 * </ul>
	 * @throws Exception if no id was provided
	 * @throws Exception if no user is in session
	 * @throws Exception if there is not any recipe with the provided id
	 * @throws Exception if the author of the recipe to be deleted from favourites is not the current user
	 * @return void
	 * @throws Exception if the recipe is not in favourites
	 */
	public function deleteFromFavourites() {
		if (!isset($_GET["id"])) {
			throw new Exception("id is mandatory");
		}
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Deleting recipes from favourites requires login");
		}
		// get the recipe object from the database
		$recipeid = $_GET["id"];
		$recipe = $this->RecipeMapper->findById($recipeid);
		// Does the recipe exist?
		if ($recipe == NULL) {
			throw new Exception("no such recipe with id: ".$recipeid);
		}
		// Check if the recipe author is the currentUser (in Session)
		if ($recipe->getAuthor() != $this->currentUser) {
			throw new Exception("recipe author is not the logged user");
		}
		// Delete the recipe from favourites
		$this->RecipeMapper->deleteFromFavourites($recipeid);
		// recipe-REDIRECT-GET
		// Everything OK, we will redirect the user to the list of recipes
		// We want to see a message after redirection, so we establish
		// a "flash" message (which is simply a Session variable) to be
		// get in the view after redirection.
		$this->view->setFlash("deletedFavOk");
		// perform the redirection. More or less:
		// header("Location: index.php?controller=recipes&action=index")
		// die();
		
		$this->view->redirect("recipes", "index");

	}



}
