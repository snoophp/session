<?php

namespace SnooPHP\Session;

/**
 * A cookie-token based session
 * 
 * The user is required to have an id column
 * Token is not require and can be set to null
 * 
 * @author Sneppy
 */
class CookieSession extends Session
{
	/**
	 * @var string $tokenFieldMap
	 */
	protected static $tokenFieldMap = [
		"hash"			=> "hash",
		"expires_at"	=> "expires_at"
	];

	/**
	 * Return user object
	 * 
	 * @return object|null
	 */
	public static function user()
	{
		return isset($_COOKIE["session_user"]) ? from_json($_COOKIE["session_user"]) : null;
	}

	/**
	 * Return session id or 0 if no active session is found
	 * 
	 * @return int
	 */
	public static function id()
	{
		return isset($_COOKIE["session_id"]) ? (int)$_COOKIE["session_id"] : 0;
	}

	/**
	 * Return session token or null if no active session is found
	 * 
	 * @return string|null
	 */
	public static function token()
	{
		return isset($_COOKIE["session_token"]) ? $_COOKIE["session_token"] : null;
	}

	/**
	 * Create a new session
	 * 
	 * Note: if set, token expiration date will be used
	 * 
	 * @param object	$user user object
	 * @param object	$token used to communicate with the application server
	 * @param bool		$stayLogged if true session won't expire
	 * 
	 * @return bool false if fails to set some cookies
	 */
	public static function createSession($user, $token = null, $stayLogged = false)
	{
		/* @todo use field map */

		$sessionMaxLength	= function_exists("env") ? env("session_max_length") : static::SESSION_MAX_LENGTH;
		$sessionDefLength	= function_exists("env") ? env("session_def_length") : static::SESSION_DEF_LENGTH;
		$hashField			= static::$tokenFieldMap["hash"];
		$expirationField	= static::$tokenFieldMap["expires_at"];
		// Calc expiration date
		$expiration = $stayLogged ?
		time() + $sessionMaxLength : (
			$token !== null && $token->$expirationField ?
			strtotime($token->$expirationField) :
			time() + $sessionDefLength
		);

		// Set session id as user id
		$status = setcookie("session_id", $user->id, $expiration, "/");
		// Store user as a json string
		$status &= setcookie("session_user", to_json($user), $expiration, "/");
		// Store token hash
		if ($token) $status &= setcookie("session_token", $token->$hashField, $expiration, "/");
		// Store stay logged flag
		$status &= setcookie("session_time", $expiration - time(), $expiration, "/");

		return $status;
	}

	/**
	 * Refresh session
	 */
	public static function refreshSession()
	{
		// Refresh using session time
		$expiration = time() + $_COOKIE["session_time"];
		
		setcookie("session_id", $_COOKIE["session_id"], $expiration, "/");
		setcookie("session_user", $_COOKIE["session_user"], $expiration, "/");
		if (static::token()) setcookie("session_token", $_COOKIE["session_token"], $expiration, "/");
		setcookie("session_time", $_COOKIE["session_time"], $expiration, "/");
	}

	/**
	 * Destroy session
	 */
	public static function destroySession()
	{
		unset($_COOKIE["session_id"]);		setcookie("session_id", "null", 1, "/");
		unset($_COOKIE["session_user"]);	setcookie("session_user", "null", 1, "/");
		unset($_COOKIE["session_token"]);	setcookie("session_token", "null", 1, "/");
		unset($_COOKIE["session_time"]);	setcookie("session_time", "null", 1, "/");
	}
}