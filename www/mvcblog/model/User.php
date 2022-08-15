<?php
// file: model/User.php

require_once(__DIR__."/../core/ValidationException.php");

/**
* Class User
*
* Represents a User in the blog
*
* @author lipido <lipido@gmail.com>
*/
class User {

	/**
	* The alias of the user
	* @var string
	*/
	private $alias;

	/**
	* The password of the user
	* @var string
	*/
	private $passwd;

	/**
	 * The email of the user
	*/
	private $email;

	/**
	* The constructor
	*
	* @param string $alias The name of the user
	* @param string $passwd The password of the user
	*/
	public function __construct($alias=NULL, $passwd=NULL) {
		$this->alias = $alias;
		$this->passwd = $passwd;
	}

	/**
	* Gets the alias of this user
	*
	* @return string The alias of this user
	*/
	public function getAlias() {
		return $this->alias;
	}

	/**
	* Sets the alias of this user
	*
	* @param string $alias The alias of this user
	* @return void
	*/
	public function setAlias($alias) {
		$this->alias = $alias;
	}

	/**
	* Gets the password of this user
	*
	* @return string The password of this user
	*/
	public function getPasswd() {
		return $this->passwd;
	}
	/**
	* Sets the password of this user
	*
	* @param string $passwd The password of this user
	* @return void
	*/
	public function setPassword($passwd) {
		$this->passwd = $passwd;
	}

	/**
	* Gets the email of this user
	*
	* @return string The email of this user
	*/
	public function getEmail() {
		return $this->email;
	}
	/**
	 * Sets the email of this user
	 * @param string $email The email of this user
	 */
	public function setEmail($email) {
		$this->email = $email;
	}


	/**
	* Checks if the current user instance is valid
	* for being registered in the database
	*
	* @throws ValidationException if the instance is
	* not valid
	*
	* @return void
	*/
	// Using php validation filters for some extra checks

	public function checkIsValidForRegister() {
		$errors = array();
		if (strlen($this->email) < 5) {
			$errors["email"] = i18n("Email must be at least 5 characters length");
		}
		if ( !filter_var($this->email, FILTER_VALIDATE_EMAIL) ) {
			$errors["email"] = i18n("Email is not valid");
		}

		if (strlen($this->alias) < 5) {
			$errors["alias"] = i18n("Alias must be at least 5 characters length");
		} else if ( !filter_var($this->alias, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z0-9]+$/"))) ) {
			$errors["alias"] = i18n("Alias is not valid (only letters and numbers)");
		}
		

		if (strlen($this->passwd) < 5) {
			$errors["passwd"] = i18n("Password must be at least 5 characters length");
		}
		//check if passwd contains at least one number and one letter
		if ( !preg_match('/[a-zA-Z]/', $this->passwd) || !preg_match('/[0-9]/', $this->passwd) ) {
			$errors["passwd"] = i18n("Password must contain at least one number and one letter");
		}
		if ( !preg_match('/^[a-zA-Z0-9]+$/', $this->passwd) ) {
			$errors["passwd"] = i18n("Password is not valid");
		}


		if (sizeof($errors)>0){
			throw new ValidationException($errors, "user is not valid");
		}
	}
}
