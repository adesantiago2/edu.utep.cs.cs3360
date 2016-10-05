<?php
	require_once("../Writable/board.php");
	require_once("../Writable/commonFunctions.php");
	$board = new board();
	
	/* Getting queries from url
	 * Reference: http://stackoverflow.com/questions/11480763/how-to-get-parameters-from-this-url-string
	 */
	$url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$parts = parse_url($url);
	parse_str($parts['query'], $query);
	
	/* getting vars from query string of url */
	$pid = $query['pid'];	
	$move = $query['move'];
	
	if ($pid == "") {
		makeError("Pid not specified");
		return;
	} else if ($move == "") {
		makeError("Move not specified");
		return;
	} else if(!doesPidExist($pid)) {
		makeError("Unknown pid");
		return;
	} else if(!validMove($move)) {
		makeError("Invalid slot, " . $move);
		return;
	}
	
	echo "Successful input!";
	setupBoard();
	$moves = makeMove($move, $pid, $board->boardOfTokensArray);
	add_to_text_doc($pid, $json);
	
	/*			Functions			 */
	
	function doesPidExist($pid) {
		return file_exists("../Writable/" . $pid . ".txt");
	}
	
	function validMove($move){
		return $move >= 0 && $move < 7;
	}