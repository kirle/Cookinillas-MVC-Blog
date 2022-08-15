<?php
// file: model/UserMapper.php

require_once(__DIR__."/../core/PDOConnection.php");

/**
* Class UserMapper
*
* Database interface for User entities
*
* @author lipido <lipido@gmail.com>
*/
class UserMapper {

	/**
	* Reference to the PDO connection
	* @var PDO
	*/
	private $db;

	public function __construct() {
		$this->db = PDOConnection::getInstance();
	}

	/**
	* Saves a User into the database
	*
	* @param User $user The user to be saved
	* @throws PDOException if a database error occurs
	* @return void
	*/
	public function save($user) {
		$stmt = $this->db->prepare("INSERT INTO users values (?,?,?)");
		$stmt->execute(array($user->getEmail(),$user->getalias(), $user->getPasswd()));
	}

	/**
	* Checks if a given alias is already in the database
	*
	* @param string $alias the alias to check
	* @return boolean true if the alias exists, false otherwise
	*/
	public function aliasExists($alias) {
		$stmt = $this->db->prepare("SELECT count(alias) FROM users where alias=?");
		$stmt->execute(array($alias));

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}

	/** 
	 * Checks if a given email is already in the database
	 * @param string $email the email to check
	 * @return boolean true if the email exists, false otherwise
	 */
	public function emailExists($email) {
		$stmt = $this->db->prepare("SELECT count(email) FROM users where email=?");
		$stmt->execute(array($email));

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}
	/**
	* Checks if a given pair of alias/password exists in the database
	*
	* @param string $alias the alias
	* @param string $passwd the password
	* @return boolean true the alias/passwrod exists, false otherwise.
	*/
	public function isValidUser($alias, $passwd) {
		$stmt = $this->db->prepare("SELECT count(alias) FROM users where alias=? and passwd=?");
		$stmt->execute(array($alias, $passwd));

		if ($stmt->fetchColumn() > 0) {
			return true;
		}
	}
}
