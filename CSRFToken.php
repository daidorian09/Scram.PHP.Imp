<?php
session_start();
class CSRFToken
{
	public static function generate_token()
	{
		$config = [
    	 "cost" => 10,
    	 "salt" => mcrypt_create_iv(64, MCRYPT_DEV_URANDOM),
		];
		$csrf_token  = password_hash("rasmuslerdorf", PASSWORD_BCRYPT, $config);
		return $_SESSION["csrf_token"] = $csrf_token;
	}
	public static function regenerate_token($csrf_form_token)
	{
		if(isset($_SESSION["csrf_token"]) && $csrf_form_token == $_SESSION["csrf_token"])
		{
			unset($_SESSION["csrf_token"]);
			return true;
		}
		return false;
	}
}

?>