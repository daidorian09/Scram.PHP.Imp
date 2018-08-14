<?php

interface IScramOperation
{
	function sign_in($user_post_data);
	function sign_up($user_post_data);
	function generate_random_salt();
	function fetch_user_data($user_info);
}

?>