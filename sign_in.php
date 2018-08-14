<?php
require_once("ScramOperation.php");


/*
@db_obj -> DbConfig class object
@csrf_token_class_object -> CSRFToken class object
@scram_obj  -> ScramOperation class object
*/

$csrf_token_class_object = new CSRFToken();
$csrf_token_class_object->regenerate_token($_POST["csrf_token"]);
$scram_obj = new ScramOperation();


/*
@scram_obj_array -> _POST data array
*/
$scram_obj_array[] = null;
$scram_obj_array = array(
					"email"=> htmlentities($_POST["email"]),
					"password"=> htmlentities($_POST["password"])
					);

/*
@scram_obj->sign_in() -> function to authenticate user
*/
$client_is_existent = null;
$authentication_is_verifed = $scram_obj->sign_in($scram_obj_array);
$client_is_existent = $authentication_is_verifed["client_is_existent"];
$authentication_is_verifed = $authentication_is_verifed["client_is_authenticated"]."<br>".$authentication_is_verifed["server_is_authenticated"];


/*if(is_null($authentication_is_verifed["client_is_existent"]))
{
	$client_is_existent = "Client not found";
}

/*
@response -> message array
*/
$response = array(
			"check" => "true",
			"client_is_existent" => $client_is_existent,
			"verifed" => $authentication_is_verifed);

/*
@return -> JSON format
*/
echo json_encode($response);		
?>