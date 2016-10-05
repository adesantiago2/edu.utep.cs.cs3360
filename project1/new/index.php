<?php
	/* This file will create a new game and a new file to save the state of the game */
	// Input: localhost:XXXX/new/?strategy=XXXXX
	// Output: {"response":true,"pid":"XXXXXXXXXXXXX"}

	/* Getting queries from url
	 * Reference: http://stackoverflow.com/questions/11480763/how-to-get-parameters-from-this-url-string
	 */
	$url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$parts = parse_url($url);
	parse_str($parts['query'], $query);
	
	/* Getting vars from query string of url */
	$strategy = $query['strategy'];
	
	if (validateStrategy($strategy)) {
		//TODO: Create a new game
		//TODO: Create a new file
		//TODO: save the state of the game to the file (might just be the board, you should be able to recreate the game based off of this)
	}
	
	
	
	/*			 Functions			 */
	
	function validateStrategy($strategy) {
		if($strategy != "Random" && $strategy != "Smart") {
			require_once("../Writable/commFunctions.php");
			makeError("Strategy not specified or wrong input");
			return false;
		}
		return true;
	}