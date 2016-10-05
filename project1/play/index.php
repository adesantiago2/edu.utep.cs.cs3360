<?php
	/* Allows the player to input a move, alters the board accordingly, and saves the altered board in a file */
	// Input: localhost:XXXX/play/?pid=XXXXXXXXXXXXX&move=X
	// Output:  {"response":true,"ack_move":{"slot":2,"isWin":false,"isDraw":false,"row":[]},"move":{"slot":4,"isWin":false,"isDraw":false,"row":[]}}

	/* Getting queries from url
	 * Reference: http://stackoverflow.com/questions/11480763/how-to-get-parameters-from-this-url-string
	 */
	$url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$parts = parse_url($url);
	parse_str($parts['query'], $query);

	/* getting vars from query string of url */
	$pid = $query['pid'];
	$move = $query['move'];
	
	require_once("../Writable/commonFunctions.php");
	
	if ($pid == "") {	// check if pid exists
		makeError("Pid not specified");
		return;
	} else if ($move == "") {	// check if move exists
		makeError("Move not specified");
		return;
	} else if(!doesPidExist($pid)) {	// check if file associated with pid exists
		makeError("Unknown pid");
		return;
	} else if(!withinRange($move)) {		// check if move is valid (within range)
		makeError("Invalid slot, " . $move);
		return;
	}
	
	
	// create instance of board loaded/parsed from pid file json
	$myfile = fopen("../"."$pid".".txt", "r+");
	fgets($myfile);//move to second line
	$jsonArray = fgets($myfile);//read second line which contains array in jason format
	$arrayDecoded = json_decode($jsonArray);
	$jsonCount = fgets($myfile);//read third line which contains count amount in jason format
	$countDecoded = json_decode($jsonCount);
	$jsonStrat = fgets($myfile);//read third line which contains count amount in jason format
	$strategyDecoded = json_decode($jsonStrat);
	$loadedBoard = loadBoard($arrayDecoded, $countDecoded, $strategyDecoded);
	fclose($myfile);
	
	//calls makeMove for user, gets the AI's decision and call makeMove for AI, saves altered board
	require_once("../Writable/board.php");
	$loadedBoard = userMove($loadedBoard, $move);
	$aiDecision = aiMove($loadedBoard);		// seperated AI move because here I get the column number
	$loadedBoard = makeMove($loadedBoard, $aiDecision);		// here I get the row array
	
	// getting response ready
	$obj = new jsonObject();
	$obj->ack_move['slot'] = $move;
	$obj->move['slot'] = $aiDecision;
	
	// check if the game is won
	$rowUser = checkIfWin($loadedBoard, 1);
	$rowAi = checkIfWin($loadedBoard, 2);
	if($rowUser.length != 0) {		// user won, row isn't empty
		$obj->ack_move['isWin'] = true;
		$obj->ack_move['row'] = $row;
		deleteFile($pid);
	} else if($rowAi.length != 0) {		// ai won, row isn't empty
		$obj->move['isWin'] = true;
		$obj->move['row'] = $row;
		deleteFile($pid);
	} else if($loadedBoard->count == $loadedBoard->maxTokens) {	// check if the game is draw
		$obj->ack_move['isDraw'] = 	true;
		$obj->move['isDraw'] = true;
		deleteFile($pid);
	}
	
	// saves altered board as json text, save into file and echo it
	$responseEncoded = json_encode($obj);
	echo $responseEncoded;
	$boardEncoded = json_encode($loadedBoard);
	addToTextDoc($pid, $responseEncoded . "\n" . $boardEncoded);
	
	
	
	/*			Functions			*/
	
	function withinRange($move){
		require_once("../Writable/board.php");
		$board = new Board();
		return $move >= 0 && $move < $board->cols;
	}
	
	function deleteFile($pid) {
		// TODO: delete the file associated with the pid to cleanup after a game is finished
	}
	
	
	/*			Classes			*/
	class jsonObject {
		public $response = true;
		public $ack_move = array("slot" => -1, "isWin" => false, "isDraw" => false, "row"=>array());	// User move
		public $move = array("slot" => -1, "isWin" => false, "isDraw" => false, "row"=>array());		// AI move
	}
	
	