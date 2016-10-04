<?php
	$url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	
	// Reference: http://stackoverflow.com/questions/11480763/how-to-get-parameters-from-this-url-string
	$parts = parse_url($url);
	parse_str($parts['query'], $query);
	
	$strategy = $query['strategy'];	// getting vars from query string of url
	$response = false;
	$reason = "";
	$pid = 0;
	
	checkForError ( $strategy, $err );
	
	$newGame = createGame ( $strategy, $newGame );	
	$jsonString = json_encode($newGame);
	
	add_to_text_doc($newGame->pid, $jsonString);
	echo $jsonString;
	
	
	
	/* Functions */
	
	/* Validates parameters, opens a file with the name of the pid.txt, and writes the json string to that file */
	function add_to_text_doc($pid, $jsonString) {
		if (!empty($pid) && !empty($jsonString)) {
			echo "test";
			$fp = fopen("../Writable/" . $pid . ".txt", "a");	// a is for writing only
			fputs($fp, nl2br($jsonString));
			fclose($fp);
		}
	}
	
	/* Checks if strategy is valid and takes appropriate actions if not */
	function checkForError($strategy, $err) {
		if($strategy != "Random" && $strategy != "Smart") {
			$err = new errorInput();
			echo json_encode($err);
			return;
		}
	}
	
	/* Creates a game based on the strategy */
	function createGame($strategy, $newGame) {
		if($strategy == "Random") {
			$newGame = new RandomStrategy();
		} else if($strategy == "Smart") {
			$newGame = new SmartStrategy();
		}
	
		$newGame->pid = uniqid();	// I have to do this here because it is dynamically created
		return $newGame;
	}
	
	
	/* Classes */
	
	class RandomStrategy {
		public $response = true;
		public $pid = 0;
	}
	
	class SmartStrategy {
		public $response = true;
		public $pid = 0;
	}
	
	class errorInput {
		public $response = false;
		public $reason = "Strategy not specified or wrong input";
	}
