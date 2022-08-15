<?php
// file: model/Ingredient.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class Ingredient
*
* Represents an Ingredient in the blog. An ingredient has an id and a name.
*
* @author kirle
*/
class Ingredient {

	/**
	* The id of this Ingredient
	* @var string
	*/
	private $id;
    /**
     * The name of this Ingredient
     * @var string
     */
    private $name;

    /**
     * The constructor
     * @param string $id The id of this Ingredient
     * @param string $name The name of this Ingredient
     */
    public function __construct($id, $name) {
        $this->id = $id;
        $this->name = $name;
    }
    /**
     * Gets the id of this Ingredient
     * @return string The id of this Ingredient
     */
    public function getId() {
        return $this->id;
    }
    /**
     * Gets the name of this Ingredient
     * @return string The name of this Ingredient
     */
    public function getName() {
        return $this->name;
    }
    /**
     * Sets the name of this Ingredient
     * @param string $name The name of this Ingredient
     * @throws ValidationException if the name is not valid
     */
    public function setName($name) {
        $this->validateName($name);
        $this->name = $name;
    }
    /**
     * Validates the name of this Ingredient
     * @param string $name The name of this Ingredient
     * @throws ValidationException if the name is not valid
     */
    private function validateName($name) {
        if ($name == NULL) {
            throw new ValidationException("The name of an ingredient cannot be null.");
        }
    }
}
