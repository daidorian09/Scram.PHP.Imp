<?php
require_once("ScramOperation.php");

/*
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
					"name"=> htmlentities($_POST["name"]),
					"lastname"=> htmlentities($_POST["lastname"]),
					"email"=> htmlentities($_POST["email"]),
					"password"=> htmlentities($_POST["password"]),
					"salt"=> sha1($scram_obj->generate_random_salt().htmlentities($_POST["answer"])),
					"iteration_count"=> mt_rand(1,10000),
					"user_signed_up"=> date('Y-m-d H:i:s')
					);

/*
@scram_obj->sign_up() -> function to insert user into database
*/
$verify_sign_up = $scram_obj->sign_up($scram_obj_array);


/*
$response -> message array
*/
$response = array(
			"check" => $verify_sign_up);
		echo json_encode($response);

?>