<?php
require_once("IScramOperation.php");
require_once("CSRFToken.php");
require_once("DbConfig.php");
class ScramOperation implements IScramOperation
{
	/*
	@db -> Class variable
	*/
	private $db;

	/*
	ScramOperation constructor 
	@db-> DbConfig class object
	*/
	function __construct()
	{
		$this->db = new DbConfig();
	}

	function sign_in($user_post_data)
	{
		/*
		@user_post_data -> user's email and password values
		@user_credentials-> user data array that is fetched from database 
		@salted_password -> PBKDF2 (in PHP hash_pbkdf2 built-in function) pseudorandom function
		@client_nonce -> generated nonce variable for client
		@server_nonce -> generated nonce variable for server
		@client_first -> client first message consisting of username and @client_nonce
		@server_first -> server first message consisting of @server_nonce, base64 encoded salt and iteration count
		@client_final_without_proof -> concatenation of @client_first and @server_first
		@authentication_message -> concatenation of @client_first, @server_first and @client_final_without_proof
		*/

		$user_credentials = $this->fetch_user_data($user_post_data["email"]);
		if($user_credentials["user_found"] == 1)
		{
		$salted_password = hash_pbkdf2("sha1", $user_post_data["password"], $user_credentials["salt"], $user_credentials["iteration_count"], 40);
		$client_nonce = $this->generate_random_salt();
		$server_nonce = $this->generate_random_salt();
		$client_first = "n=".$user_post_data["email"].",r=".$client_nonce;
		$server_first = "r=".$server_nonce.",s=".base64_encode($user_credentials["salt"]).",i=".$user_credentials["iteration_count"];
		$client_final_without_proof = "c=biws,r=".$client_nonce.$server_nonce;
		$authentication_message = $client_first.",".$server_first.",".$client_final_without_proof;

		/*
		@client_key -> using hash_hmac function generating a keyed hash value with parameters (hash algorithm such as md5,sha1,sha512,joaat,ripemd128 etc.., password, and verbatim string such as 'asd',\I'am accomplished engin'ee'r\\')
		@server_key -> using hash_hmac function generating a keyed hash value with parameters (hash algorithm such as md5,sha1,sha512,joaat,ripemd128 etc.., password, and verbatim string such as 'asd',\I'am accomplished engin'ee'r\\')
		*/
		$client_key = hash_hmac("sha1", $salted_password, "'Client Key'");
		$server_key = hash_hmac("sha1", $salted_password, "'Server Key'");

		/*
		@hashed_client_key -> sha1 hashed value of @client_key
		*/
		$hashed_client_key = sha1($client_key);

		/*
		@client_proof -> @client_key xor hash_hmac value using @hashed_client_key and @authentication_message
		@server_proof -> hash_hmac value using @server_key and @authentication_message
		*/
		$client_proof = bin2hex(pack('H*',$client_key) ^ pack('H*',hash_hmac("sha1", $hashed_client_key, $authentication_message)));
		$server_proof = hash_hmac("sha1", $server_key, $authentication_message);

		/*
		@client_stored_key_proof -> @client_key xor hash_hmac value using @user_credentials["password"] which is stored password in the database and @authentication_message
		@server_stored_key_proof -> @client_key xor hash_hmac value using @user_credentials["server_key"] which is stored server key in the database and @authentication_message
		*/
		$client_stored_key_proof = bin2hex(pack('H*',$client_key) ^ pack('H*',hash_hmac("sha1", $user_credentials["password"], $authentication_message)));
		$server_stored_key_proof = hash_hmac("sha1", $user_credentials["server_key"], $authentication_message);

		/*
		@client_is_authenticated -> if 	@client_proof and @client_stored_key_proof are equivalent then returning a message which is client is authenticated or else client is not authenticated
		@server_is_authenticated -> if 	@server_proof and @server_stored_key_proof are equivalent then returning a message which is server is authenticated or else server is not authenticated
		*/
		$client_is_authenticated = $client_proof == $client_stored_key_proof ? "Client is authenticated" : "Client is unautherized";
		$server_is_authenticated = $server_proof == $server_stored_key_proof ? "Server is authenticated" : "Server is unautherized";

		/*
		@return both client and server authentication
		*/
		$authentication_response_message = array(
			"client_is_existent" => true,
			"client_is_authenticated" => $client_is_authenticated, 
			"server_is_authenticated" => $server_is_authenticated);

		$client_final = $authentication_message.",p=".base64_encode($client_proof);


		//print_r("<pre>");
		/*print_r("Salted Password : ".$salted_password."\nHashed Password : ".$hashed_password."\nClient Nonce : ".$client_nonce."\nServer Nonce : ".$server_nonce."\nClient First : ".$client_first."\nServer First : ".$server_first."\nClient Final Without Proof : ".$client_final_without_proof."\n Authentication Message ".$authentication_message."\nClient Key : ".$client_key."\nServer Key : ".$server_key."\nHashed Client : ".$hashed_client_key."\nClient Proof : ".$client_proof."\nServer Proof : ".$server_proof."\nClient Final : ".$client_final)."\nBin2Hex Client : ".bin2hex(pack('H*',$client_key))."\nBin2Hex Hashed Client : ".bin2hex(pack('H*',hash_hmac("sha1", $hashed_client_key, $authentication_message)));*/
	//	/print("Server Base64 : ".base64_encode($server_proof)."\nServer Proof : ".$server_proof);
		//print_r("</pre>");
		return $authentication_response_message;
		}
		else
		{
			$authentication_response_message = array(
			"client_is_existent" => null,
			"client_is_authenticated" => "Client is unautherized", 
			"server_is_authenticated" => "Server is unautherized");
			return $authentication_response_message;
		}
	}

	/*
	Function to sign up user
	@user_post_data -> mass assignment values
	@salted_password ->  PBKDF2 (in PHP hash_pbkdf2 built-in function) pseudorandom function
	@client_key -> using hash_hmac function generating a keyed hash value with parameters (hash algorithm such as md5,sha1,sha512,joaat,ripemd128 etc.., password, and verbatim string such as 'asd',\I'am accomplished engin'ee'r\\')
	@server_key -> using hash_hmac function generating a keyed hash value with parameters (hash algorithm such as md5,sha1,sha512,joaat,ripemd128 etc.., password, and verbatim string such as 'asd',\I'am accomplished engin'ee'r\\')
	@hashed_password -> sha1 hashed value of @client_key
	@user_post_data["password"] -> replaced with @hashed_password
	@query_builder -> database connecter
	@query -> SQL statement
	@this->db->db_disconnect() -> method to terminate existent database connection
	*/
	function sign_up($user_post_data)
	{
		$salted_password = hash_pbkdf2("sha1", $user_post_data["password"], $user_post_data["salt"], $user_post_data["iteration_count"], 40);
		$client_key = hash_hmac("sha1", $salted_password, "'Client Key'");
		$server_key = hash_hmac("sha1", $salted_password, "'Server Key'");
		$hashed_password = sha1($client_key);
		$user_post_data["password"] = $hashed_password;

		$query_builder= $this->db->db_connect();
		$query = $query_builder->prepare("insert into user(name,lastname,email,password,server_key,salt,iteration_count,user_created) values(?,?,?,?,?,?,?,?)");
		$query->execute(array($user_post_data["name"], $user_post_data["lastname"], $user_post_data["email"], $user_post_data["password"],$server_key, $user_post_data["salt"], $user_post_data["iteration_count"], $user_post_data["user_signed_up"]));
		return true;
		$this->db->db_disconnect();
		
	}

	/*
	@return a nonce or salt value
	*/
	function generate_random_salt($length = 10)
	{
		  return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/*-!^#+$%&{}[]-:|~', ceil($length/strlen($x)) )),1,$length);
	}

	/*
	@user_info -> user's email address
	@query_builder -> database connecter
	@query -> SQL statement
	@rows -> fetched data from database
	@user_has -> array that is filled with @rows
	@return @user_has required user data consisting of password, server key, salt and iteration count
	*/
	function fetch_user_data($user_info)
	{
		$query_builder= $this->db->db_connect();
		$query = $query_builder->prepare("select count(*) as user_found,u.password,u.server_key,u.salt,u.iteration_count from user u where u.email = ?");
		$query->execute(array($user_info));
		$rows = $query->fetchAll(PDO::FETCH_ASSOC);
		$user_has[] = null;
		foreach ($rows as $row) 
		{
			$user_has =  array(
				"user_found" => $row["user_found"],
				"password" => $row["password"],
				"server_key" => $row["server_key"],
				"salt" => $row["salt"],
				"iteration_count" => $row["iteration_count"]
				);
		}
		$this->db->db_disconnect();
		return $user_has;
	}
}


