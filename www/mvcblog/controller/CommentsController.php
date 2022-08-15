<?php
//file: /controller/CommentsController.php

require_once(__DIR__."/../model/User.php");
require_once(__DIR__."/../model/Recipe.php");
require_once(__DIR__."/../model/Comment.php");

require_once(__DIR__."/../model/RecipeMapper.php");
require_once(__DIR__."/../model/CommentMapper.php");

require_once(__DIR__."/../controller/BaseController.php");

/**
* Class CommentsController
*
* Controller for comments related use cases.
*
* @author lipido <lipido@gmail.com>
*/
class CommentsController extends BaseController {

	/**
	* Reference to the CommentMapper to interact
	* with the database
	*
	* @var CommentMapper
	*/
	private $commentmapper;

	/**
	* Reference to the RecipeMapper to interact
	* with the database
	*
	* @var RecipeMapper
	*/
	private $recipeMapper;

	public function __construct() {
		parent::__construct();

		$this->commentmapper = new CommentMapper();
		$this->recipeMapper = new RecipeMapper();
	}

	/**
	* Action to adds a comment to a recipe
	*
	* This method should only be called via HTTP POST.
	*
	* The user of the comment is taken from the {@link BaseController::currentUser}
	* property.
	* The expected HTTP parameters are:
	* <ul>
	* <li>id: Id of the recipe (via HTTP POST)</li>
	* <li>content: Content of the comment (via HTTP POST)</li>
	* </ul>
	*
	* The views are:
	* <ul>
	* <li>recipes/view?id=recipe: If comment was successfully added of,
	* or if it was not validated (via redirect). Includes these view variables:</li>
	* <ul>
	*	<li>errors (flash): Array including per-field validation errors</li>
	*	<li>comment (flash): The current Comment instance, empty or being added</li>
	* </ul>
	* </ul>
	*
	* @return void
	*/
	public function add() {
		if (!isset($this->currentUser)) {
			throw new Exception("Not in session. Adding recipes requires login");
		}

		if (isset($_POST["id"])) { // reaching via HTTP Recipe...

			// Get the Recipe object from the database
			$recipeid = $_POST["id"];
			$recipe = $this->recipeMapper->findById($recipeid);

			// Does the recipe exist?
			if ($recipe == NULL) {
				throw new Exception("no such recipe with id: ".$recipeid);
			}

			// Create and populate the Comment object
			$comment = new Comment();
			$comment->setContent($_POST["content"]);
			$comment->setAuthor($this->currentUser);
			$comment->setRecipe($recipe);

			try {

				// validate Comment object
				$comment->checkIsValidForCreate(); // if it fails, ValidationException

				// save the Comment object into the database
				$this->commentmapper->save($comment);

				// POST-REDIRECT-GET
				// Everything OK, we will redirect the user to the list of recipes
				// We want to see a message after redirection, so we establish
				// a "flash" message (which is simply a Session variable) to be
				// get in the view after redirection.
				$this->view->setFlash("Comment \"".$recipe ->getTitle()."\" successfully added.");

				// perform the redirection. More or less:
				// header("Location: index.php?controller=recipes&action=view&id=$recipeid")
				// die();
				$this->view->redirect("recipes", "view", "id=".$recipe->getId());
			}catch(ValidationException $ex) {
				$errors = $ex->getErrors();

				// Go back to the form to show errors.
				// However, the form is not in a single page (comments/add)
				// It is in the View Recipe page.
				// We will save errors as a "flash" variable (third parameter true)
				// and redirect the user to the referring page
				// (the View recipe page)
				$this->view->setVariable("comment", $comment, true);
				$this->view->setVariable("errors", $errors, true);

				$this->view->redirect("recipes", "view", "id=".$recipe->getId());
			}
		} else {
			throw new Exception("No such recipe id");
		}
	}
}
