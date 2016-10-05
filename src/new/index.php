<?php
	require_once("../Writable/board.php");
	$board = new board();

	/* Getting queries from url
	 * Reference: http://stackoverflow.com/questions/11480763/how-to-get-parameters-from-this-url-string
	 */
	$url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$parts = parse_url($url);
	parse_str($parts['query'], $query);
	
	/* Getting vars from query string of url */
	$strategy = $query['strategy'];
	
	if (!checkForError ($strategy, $err)) {
		$newGame = createGame ( $strategy, $newGame );	
		$jsonString = json_encode($newGame);
		
		require_once("../Writable/commonFunctions.php");
		add_to_text_doc($newGame->pid, $jsonString);
		echo $jsonString;
	}
	
	/*			 Functions			 */
	
	/* Checks if strategy is valid and takes appropriate actions if not */
	function checkForError($strategy, $err) {
		if($strategy != "Random" && $strategy != "Smart") {
			require_once("../Writable/commonFunctions.php");
			makeError("Strategy not specified or wrong input");
			return true;
		}
		return false;
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
